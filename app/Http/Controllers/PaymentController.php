<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function createForBooking($idTiket)
    {
        $user = Auth::guard('pengunjung')->user();

        $tiket = Tiket::with(['tempat', 'addons', 'pembayaran'])
            ->where('ID_TIKET', $idTiket)
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->firstOrFail();

        // kalau sudah ada pembayaran, langsung ke halaman pay
        if ($tiket->pembayaran) {
            return redirect("/payment/{$tiket->pembayaran->ID_PEMBAYARAN}/pay");
        }

        // hitung total (sama seperti di BookingController@show)
        $mulai = Carbon::parse($tiket->TANGGAL_MULAI);
        $selesai = Carbon::parse($tiket->TANGGAL_SELESAI);
        $nights = max(1, $mulai->diffInDays($selesai));

        $totalTempat = (float)$tiket->tempat->HARGA_PER_MALAM * $nights;
        $totalAddon = 0;
        foreach ($tiket->addons as $ad) {
            $totalAddon += ((float)$ad->HARGA_BARANG * (int)($ad->QTY ?? 1) * $nights);
        }
        $grandTotal = $totalTempat + $totalAddon;

        $pay = Pembayaran::create([
            'ID_TIKET' => $tiket->ID_TIKET,
            'TANGGAL_PEMBAYARAN' => null,
            'JUMLAH_PEMBAYARAN' => $grandTotal,
            'METODE_BAYAR' => 'DUMMY',
            'STATUS_BAYAR' => 'PENDING',
            'BUKTI_PEMBAYARAN' => null,
        ]);

        return redirect("/payment/{$pay->ID_PEMBAYARAN}/pay")->with('ok', 'Payment dummy dibuat.');
    }

    public function payPage($idPembayaran)
    {
        $user = Auth::guard('pengunjung')->user();

        $pay = Pembayaran::with('tiket.tempat', 'tiket.addons')
            ->where('ID_PEMBAYARAN', $idPembayaran)
            ->firstOrFail();

        // pastikan payment ini milik user
        if ($pay->tiket->ID_PENGUNJUNG != $user->ID_PENGUNJUNG) abort(403);

        return view('payment.pay', compact('pay'));
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

            // set tempat jadi BOOKED (kalau kamu mau)
            $tempat = $pay->tiket->tempat;
            if ($tempat) {
                $tempat->STATUS = 'BOOKED';
                $tempat->save();
            }
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
