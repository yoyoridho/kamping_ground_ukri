<?php

namespace App\Http\Controllers;

use App\Models\HasilTiket;
use App\Models\Tiket;
use App\Models\BarangKamping;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingWizardController extends Controller
{
    private string $sessionKey = 'booking_wizard';

    /**
     * Cari booking yang masih menggantung (belum LUNAS).
     */
    private function blockingBooking(int $idPengunjung): ?Tiket
    {
        return Tiket::with('pembayaran')
            ->where('ID_PENGUNJUNG', $idPengunjung)
            ->where(function ($q) {
                $q->whereDoesntHave('pembayaran')
                    ->orWhereHas('pembayaran', function ($qq) {
                        $qq->where('STATUS_BAYAR', '!=', 'LUNAS');
                    });
            })
            ->orderByDesc('ID_TIKET')
            ->first();
    }

    public function step1(Request $request)
    {
        $user = auth('pengunjung')->user();
        if ($user) {
            $blocking = $this->blockingBooking((int) $user->ID_PENGUNJUNG);
            if ($blocking) {
                return redirect('/booking/'.$blocking->ID_TIKET)
                    ->with('ok', 'Kamu masih punya booking yang belum LUNAS. Silakan bayar dulu atau cancel booking tersebut.');
            }
        }
        if ($user) {
            $blocking = $this->blockingBooking((int) $user->ID_PENGUNJUNG);
            if ($blocking) {
                return redirect('/booking/'.$blocking->ID_TIKET)
                    ->with('ok', 'Kamu masih punya booking yang belum LUNAS. Silakan bayar dulu atau cancel booking tersebut.');
            }
        }

        $tempatList = HasilTiket::orderByDesc('ID_TEMPAT')->get();

        $selectedTempatId = (int) $request->query('tempat', 0);

        $old = $request->session()->get($this->sessionKey, []);
        if (($old['ID_TEMPAT'] ?? 0) && !$selectedTempatId) {
            $selectedTempatId = (int) $old['ID_TEMPAT'];
        }

        return view('booking.wizard_step1', compact('tempatList', 'selectedTempatId', 'old'));
    }


    public function postStep1(Request $request)
    {
        $user = auth('pengunjung')->user();
        if ($user) {
            $blocking = $this->blockingBooking((int) $user->ID_PENGUNJUNG);
            if ($blocking) {
                return redirect('/booking/'.$blocking->ID_TIKET)
                    ->with('ok', 'Kamu masih punya booking yang belum LUNAS. Silakan bayar dulu atau cancel booking tersebut.');
            }
        }

        $data = $request->validate([
            'ID_TEMPAT' => 'required|integer',
            'TANGGAL_MULAI' => 'required|date',
            'TANGGAL_SELESAI' => 'required|date|after:TANGGAL_MULAI',
            'JUMLAH_ORANG' => 'required|integer|min:1',
        ]);

        $request->session()->put($this->sessionKey, $data);

        return redirect()->route('booking.wizard.step2');
    }

    public function step2(Request $request)
    {
        $wizard = $request->session()->get($this->sessionKey);

        if (!$wizard) {
            return redirect()->route('booking.wizard.step1')
                ->with('ok', 'Silakan isi data booking dulu.');
        }

        $tempat = HasilTiket::find($wizard['ID_TEMPAT']);

        $rentalItems = Fasilitas::where('STATUS', 'AKTIF')
            ->where('STOK', '>', 0)
            ->orderBy('NAMA_FASILITAS')
            ->get()
            ->map(fn($x) => [
                'id' => (int) $x->ID_FASILITAS,
                'name' => (string) $x->NAMA_FASILITAS,
                'price' => (int) $x->HARGA_FASILITAS,
                'stok' => (int) $x->STOK,
         ])
            ->toArray();


        return view('booking.wizard_step2', compact('wizard', 'tempat', 'rentalItems'));
    }

    public function finish(Request $request)
    {
        $wizard = $request->session()->get($this->sessionKey);

        if (!$wizard) {
            return redirect()->route('booking.wizard.step1')
                ->with('ok', 'Silakan isi data booking dulu.');
        }

        $data = $request->validate([
            'addons' => 'sometimes|array',
            'addons.*' => 'nullable|integer|min:0', // addons[ID] = qty
        ]);


        $user = auth('pengunjung')->user();

        $blocking = $this->blockingBooking((int) $user->ID_PENGUNJUNG);
        if ($blocking) {
            return redirect('/booking/'.$blocking->ID_TIKET)
                ->with('ok', 'Kamu masih punya booking yang belum LUNAS. Silakan bayar dulu atau cancel booking tersebut.');
        }

        $createdTiketId = null;

        try {
            DB::transaction(function () use ($user, $wizard, $data, &$createdTiketId) {

            $tiket = Tiket::create([
                'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
                'ID_TEMPAT' => $wizard['ID_TEMPAT'],
                'TANGGAL_MULAI' => $wizard['TANGGAL_MULAI'],
                'TANGGAL_SELESAI' => $wizard['TANGGAL_SELESAI'],
                'JUMLAH_ORANG' => $wizard['JUMLAH_ORANG'],
            ]);

            $createdTiketId = $tiket->ID_TIKET;

            $addons = $data['addons'] ?? [];
if (!is_array($addons)) $addons = [];

// ambil yang qty > 0
$picked = [];
foreach ($addons as $fid => $qty) {
    $fid = (int) $fid;
    $qty = (int) $qty;
    if ($fid > 0 && $qty > 0) {
        $picked[$fid] = $qty;
    }
}

if (count($picked) > 0) {
    // lock fasilitas yang dipilih
    $facilities = Fasilitas::whereIn('ID_FASILITAS', array_keys($picked))
        ->where('STATUS', 'AKTIF')
        ->get()
        ->keyBy('ID_FASILITAS');

    // validasi stok cukup
    foreach ($picked as $fid => $qty) {
        if (!isset($facilities[$fid])) {
            // fasilitas tidak ada / tidak aktif
            throw new \Exception("Fasilitas tidak valid.");
        }

        $f = $facilities[$fid];
        $stok = (int) $f->STOK;

        if ($stok < $qty) {
            // lempar exception biar transaksi batal
            throw new \Exception("Stok {$f->NAMA_FASILITAS} tidak cukup. Sisa: {$stok}");
        }
    }

    // simpan addons (stok akan dipotong saat pembayaran LUNAS)
    foreach ($picked as $fid => $qty) {
        $f = $facilities[$fid];

        BarangKamping::create([
            'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
            'ID_TEMPAT' => $wizard['ID_TEMPAT'],
            'ID_TIKET' => $tiket->ID_TIKET,
            'ID_FASILITAS' => $fid,

            // supaya tetap kompatibel sama tabel barang_kamping kamu:
            'NAMA_BARANG' => (string) $f->NAMA_FASILITAS,
            'HARGA_BARANG' => (string) ((int) $f->HARGA_FASILITAS),
            'QTY' => $qty,
        ]);
    }
}

            });
        } catch (\Throwable $e) {
            // batal, jangan hapus session wizard supaya user bisa perbaiki qty
            return back()->withInput()->withErrors([
                'addons' => $e->getMessage(),
            ]);
        }

        // bersihkan session wizard
        $request->session()->forget($this->sessionKey);

        return redirect('/booking/'.$createdTiketId)
            ->with('ok', 'Booking selesai! Silakan lakukan pembayaran untuk menyelesaikan booking kamu.');
    }
}