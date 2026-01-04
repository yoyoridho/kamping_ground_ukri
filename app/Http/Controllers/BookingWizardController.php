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

    public function step1(Request $request)
    {
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
            ->orderBy('NAMA_FASILITAS')
            ->get()
            ->map(fn($x) => [
                'name' => $x->NAMA_FASILITAS,
                'price' => (int) $x->HARGA_FASILITAS,
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
            'addons.*.name' => 'sometimes|string',
            'addons.*.price' => 'sometimes|numeric|min:0',
            'addons.*.qty' => 'nullable|integer|min:0',
        ]);

        $user = auth('pengunjung')->user();

        $activeFacilities = Fasilitas::where('STATUS', 'AKTIF')
            ->get()
            ->keyBy('NAMA_FASILITAS');

        $createdTiketId = null;

        DB::transaction(function () use ($user, $wizard, $data, $activeFacilities, &$createdTiketId) {

            $tiket = Tiket::create([
                'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
                'ID_TEMPAT' => $wizard['ID_TEMPAT'],
                'TANGGAL_MULAI' => $wizard['TANGGAL_MULAI'],
                'TANGGAL_SELESAI' => $wizard['TANGGAL_SELESAI'],
                'JUMLAH_ORANG' => $wizard['JUMLAH_ORANG'],
            ]);

            $createdTiketId = $tiket->ID_TIKET;

            $addons = $data['addons'] ?? [];

            foreach ($addons as $a) {
                $qty = (int)($a['qty'] ?? 0);
                if ($qty <= 0) continue;

                $name = (string)($a['name'] ?? '');

                if (!$activeFacilities->has($name)) {
                    continue;
                }

                $hargaAsli = (int) $activeFacilities[$name]->HARGA_FASILITAS;

                BarangKamping::create([
                    'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
                    'ID_TEMPAT' => $wizard['ID_TEMPAT'],
                    'ID_TIKET' => $tiket->ID_TIKET,
                    'NAMA_BARANG' => $name,
                    'HARGA_BARANG' => (string) $hargaAsli,
                    'QTY' => $qty,
                ]);
            }

            session()->forget('booking_wizard');
        });

        return redirect('/booking')->with('ok', 'Booking berhasil disimpan! (ID: '.$createdTiketId.')');
    }
}
