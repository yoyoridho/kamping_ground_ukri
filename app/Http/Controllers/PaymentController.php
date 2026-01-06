<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function initMidtrans(): void
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    private function mapMidtransToStatusBayar(?string $transactionStatus, ?string $fraudStatus = null): string
    {
        $ts = strtolower((string) $transactionStatus);
        $fs = strtolower((string) $fraudStatus);

        if ($ts === 'capture') {
            return ($fs === 'challenge') ? 'PENDING' : 'LUNAS';
        }
        if ($ts === 'settlement') return 'LUNAS';
        if ($ts === 'pending') return 'PENDING';
        if ($ts === 'deny') return 'GAGAL';
        if ($ts === 'cancel') return 'GAGAL';
        if ($ts === 'expire') return 'EXPIRED';
        if ($ts === 'refund' || $ts === 'partial_refund') return 'REFUND';

        return 'PENDING';
    }

    /**
     * ✅ Tambahan QR:
     * Pastikan tiket punya QR_TOKEN jika status bayar LUNAS.
     * Tidak akan overwrite token yang sudah ada.
     */
    private function ensureQrTokenForTiket(int $idTiket): void
    {
        $tiket = Tiket::find($idTiket);
        if (!$tiket) return;

        // hanya generate kalau sudah lunas
        // (pakai STATUS_BAYAR dari pembayaran sebagai sumber kebenaran)
        if (empty($tiket->QR_TOKEN)) {
            $tiket->QR_TOKEN = (string) Str::uuid();
        }

        // inisialisasi check-in field jika ada kolomnya
        // (aman meskipun kolom belum ada, tapi sebaiknya migrasi sudah dibuat)
        if (property_exists($tiket, 'CHECKIN_STATUS') || isset($tiket->CHECKIN_STATUS)) {
            if (empty($tiket->CHECKIN_STATUS)) $tiket->CHECKIN_STATUS = 'BELUM';
        }

        $tiket->save();
    }

    public function createForBooking($idTiket)
    {
        $user = Auth::guard('pengunjung')->user();

        $tiket = Tiket::with(['tempat', 'addons', 'pembayaran'])
            ->where('ID_TIKET', $idTiket)
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->firstOrFail();

        if ($tiket->pembayaran) {
            return redirect("/payment/{$tiket->pembayaran->ID_PEMBAYARAN}/pay");
        }

        $mulai = Carbon::parse($tiket->TANGGAL_MULAI);
        $selesai = Carbon::parse($tiket->TANGGAL_SELESAI);
        $nights = max(1, $mulai->diffInDays($selesai));

        $totalTempat = (float) ($tiket->tempat->HARGA_PER_MALAM ?? 0) * $nights;
        $totalAddon = 0;
        foreach ($tiket->addons as $ad) {
            $totalAddon += ((float) ($ad->HARGA_BARANG ?? 0) * (int) ($ad->QTY ?? 1) * $nights);
        }
        $grandTotal = $totalTempat + $totalAddon;

        $pay = Pembayaran::create([
            'ID_TIKET' => $tiket->ID_TIKET,
            'TANGGAL_PEMBAYARAN' => null,
            'JUMLAH_PEMBAYARAN' => $grandTotal,
            'METODE_BAYAR' => 'MIDTRANS',
            'STATUS_BAYAR' => 'PENDING',
            'BUKTI_PEMBAYARAN' => null,

            'MIDTRANS_ORDER_ID' => null,
            'MIDTRANS_SNAP_TOKEN' => null,
            'MIDTRANS_TRANSACTION_STATUS' => null,
            'MIDTRANS_PAYMENT_TYPE' => null,
            'MIDTRANS_RAW_NOTIFICATION' => null,
        ]);

        return redirect("/payment/{$pay->ID_PEMBAYARAN}/pay")->with('ok', 'Payment dibuat, siap bayar via Midtrans.');
    }

    public function payPage($idPembayaran)
    {
        $user = Auth::guard('pengunjung')->user();

        $pay = Pembayaran::with('tiket.tempat', 'tiket.addons')
            ->where('ID_PEMBAYARAN', $idPembayaran)
            ->firstOrFail();

        if ($pay->tiket->ID_PENGUNJUNG != $user->ID_PENGUNJUNG) abort(403);

        $snapToken = $pay->MIDTRANS_SNAP_TOKEN;

        if (!$snapToken && $pay->STATUS_BAYAR === 'PENDING') {
            $this->initMidtrans();

            $orderId = $pay->MIDTRANS_ORDER_ID;
            if (!$orderId) {
                $orderId = 'UKRI-' . $pay->ID_PEMBAYARAN . '-' . Str::upper(Str::random(8));
                $pay->MIDTRANS_ORDER_ID = $orderId;
            }

            $nights = 1;
            try {
                $mulai = Carbon::parse($pay->tiket->TANGGAL_MULAI);
                $selesai = Carbon::parse($pay->tiket->TANGGAL_SELESAI);
                $nights = max(1, $mulai->diffInDays($selesai));
            } catch (\Throwable $e) {
                $nights = 1;
            }

            $items = [];
            if ($pay->tiket->tempat) {
                $items[] = [
                    'id' => 'TEMPAT-' . ($pay->tiket->tempat->ID_TEMPAT ?? 'X'),
                    'price' => (int) round((float) ($pay->tiket->tempat->HARGA_PER_MALAM ?? 0)),
                    'quantity' => (int) $nights,
                    'name' => (string) ($pay->tiket->tempat->NAMA_TEMPAT ?? 'Tempat Camping'),
                ];
            }
            foreach ($pay->tiket->addons as $ad) {
                $items[] = [
                    'id' => 'ADDON-' . ($ad->ID_BARANG ?? Str::random(6)),
                    'price' => (int) round((float) ($ad->HARGA_BARANG ?? 0)),
                    'quantity' => (int) (($ad->QTY ?? 1) * $nights),
                    'name' => (string) ($ad->NAMA_BARANG ?? 'Addon'),
                ];
            }

            $cust = Auth::guard('pengunjung')->user();
            $customerDetails = [
                'first_name' => (string) ($cust->NAMA_PENGUNJUNG ?? 'Pengunjung'),
                'email' => (string) ($cust->EMAIL ?? $cust->email ?? 'pengunjung@example.com'),
                'phone' => (string) ($cust->NO_HP ?? $cust->no_hp ?? ''),
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) round((float) $pay->JUMLAH_PEMBAYARAN),
                ],
                'customer_details' => $customerDetails,
            ];

            if (!empty($items)) $params['item_details'] = $items;

            $finishUrl = config('midtrans.finish_url');
            if ($finishUrl) {
                $params['callbacks'] = ['finish' => $finishUrl];
            }

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $pay->MIDTRANS_SNAP_TOKEN = $snapToken;
            $pay->save();
        }

        $clientKey = config('midtrans.client_key');
        $isProduction = (bool) config('midtrans.is_production');

        return view('payment.pay', compact('pay', 'snapToken', 'clientKey', 'isProduction'));
    }

    public function storeResult(Request $request, $idPembayaran)
    {
        $user = Auth::guard('pengunjung')->user();

        $pay = Pembayaran::with('tiket.tempat')
            ->where('ID_PEMBAYARAN', $idPembayaran)
            ->firstOrFail();

        if ($pay->tiket->ID_PENGUNJUNG != $user->ID_PENGUNJUNG) abort(403);

        $data = $request->validate([
            'order_id' => 'nullable|string',
            'transaction_status' => 'nullable|string',
            'fraud_status' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'raw' => 'nullable',
        ]);

        if (!empty($data['order_id'])) $pay->MIDTRANS_ORDER_ID = $data['order_id'];
        if (!empty($data['transaction_status'])) $pay->MIDTRANS_TRANSACTION_STATUS = $data['transaction_status'];
        if (!empty($data['payment_type'])) $pay->MIDTRANS_PAYMENT_TYPE = $data['payment_type'];
        if (array_key_exists('raw', $data)) $pay->MIDTRANS_RAW_NOTIFICATION = json_encode($data['raw']);

        $statusBayar = $this->mapMidtransToStatusBayar($data['transaction_status'] ?? null, $data['fraud_status'] ?? null);
        $pay->STATUS_BAYAR = $statusBayar;

        if ($statusBayar === 'LUNAS') {
            $pay->TANGGAL_PEMBAYARAN = now()->toDateString();

            // set tempat jadi BOOKED
            $tempat = $pay->tiket->tempat;
            if ($tempat) {
                $tempat->STATUS = 'BOOKED';
                $tempat->save();
            }

            // ✅ QR: generate token untuk tiket yang lunas
            $this->ensureQrTokenForTiket((int) $pay->ID_TIKET);
        }

        $pay->save();

        return response()->json(['ok' => true, 'status_bayar' => $pay->STATUS_BAYAR]);
    }

    public function notification(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Throwable $e) {
            return response('invalid', 200);
        }

        $orderId = $notif->order_id ?? null;
        $transactionStatus = $notif->transaction_status ?? null;
        $fraudStatus = $notif->fraud_status ?? null;
        $paymentType = $notif->payment_type ?? null;

        if (!$orderId) return response('no order_id', 200);

        $pay = Pembayaran::where('MIDTRANS_ORDER_ID', $orderId)->first();
        if (!$pay) return response('order not found', 200);

        $pay->MIDTRANS_TRANSACTION_STATUS = $transactionStatus;
        $pay->MIDTRANS_PAYMENT_TYPE = $paymentType;
        $pay->MIDTRANS_RAW_NOTIFICATION = json_encode($notif);

        $statusBayar = $this->mapMidtransToStatusBayar($transactionStatus, $fraudStatus);
        $pay->STATUS_BAYAR = $statusBayar;

        if ($statusBayar === 'LUNAS' && empty($pay->TANGGAL_PEMBAYARAN)) {
            $pay->TANGGAL_PEMBAYARAN = now()->toDateString();

            // set tempat jadi BOOKED
            $tiket = Tiket::with('tempat')->where('ID_TIKET', $pay->ID_TIKET)->first();
            if ($tiket && $tiket->tempat) {
                $tiket->tempat->STATUS = 'BOOKED';
                $tiket->tempat->save();
            }

            // ✅ QR: generate token untuk tiket yang lunas (webhook source of truth)
            $this->ensureQrTokenForTiket((int) $pay->ID_TIKET);
        }

        $pay->save();

        return response('ok', 200);
    }

    public function simulate(Request $request, $idPembayaran)
    {
        $user = Auth::guard('pengunjung')->user();

        $data = $request->validate([
            'result' => 'required|in:success,failed,expired'
        ]);

        $pay = Pembayaran::with('tiket.tempat')
            ->where('ID_PEMBAYARAN', $idPembayaran)
            ->firstOrFail();

        if ($pay->tiket->ID_PENGUNJUNG != $user->ID_PENGUNJUNG) abort(403);

        if ($data['result'] === 'success') {
            $pay->STATUS_BAYAR = 'LUNAS';
            $pay->TANGGAL_PEMBAYARAN = now()->toDateString();

            $tempat = $pay->tiket->tempat;
            if ($tempat) {
                $tempat->STATUS = 'BOOKED';
                $tempat->save();
            }

            // ✅ QR: generate token untuk tiket lunas dari simulasi
            $this->ensureQrTokenForTiket((int) $pay->ID_TIKET);
        } elseif ($data['result'] === 'failed') {
            $pay->STATUS_BAYAR = 'GAGAL';
        } else {
            $pay->STATUS_BAYAR = 'EXPIRED';
        }

        $pay->save();

        return redirect("/booking/{$pay->ID_TIKET}")
            ->with('ok', "Simulasi pembayaran: {$pay->STATUS_BAYAR}");
    }
}
