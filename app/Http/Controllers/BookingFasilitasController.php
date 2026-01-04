<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\BarangKamping;
use Illuminate\Http\Request;

class BookingFasilitasController extends Controller
{
    private array $rentalItems = [
        ['name' => 'Tenda 2 Orang', 'price' => 50000],
        ['name' => 'Tenda 4 Orang', 'price' => 80000],
        ['name' => 'Sleeping Bag', 'price' => 20000],
        ['name' => 'Matras', 'price' => 15000],
        ['name' => 'Kompor Portable', 'price' => 30000],
    ];

    public function edit($id)
    {
        $user = auth('pengunjung')->user();

        $tiket = Tiket::with(['tempat', 'addons'])
            ->where('ID_TIKET', $id)
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->firstOrFail();

        $existing = $tiket->addons->keyBy('NAMA_BARANG');

        return view('booking.fasilitas', [
            'tiket' => $tiket,
            'rentalItems' => $this->rentalItems,
            'existing' => $existing,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth('pengunjung')->user();

        $tiket = Tiket::with('tempat')
            ->where('ID_TIKET', $id)
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->firstOrFail();

        $data = $request->validate([
            'addons' => 'required|array',
            'addons.*.name' => 'required|string',
            'addons.*.price' => 'required|numeric|min:0',
            'addons.*.qty' => 'nullable|integer|min:0',
        ]);

        BarangKamping::where('ID_TIKET', $tiket->ID_TIKET)->delete();

        foreach ($data['addons'] as $a) {
            $qty = (int)($a['qty'] ?? 0);
            if ($qty <= 0) continue; 

            BarangKamping::create([
                'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
                'ID_TEMPAT' => $tiket->ID_TEMPAT,
                'ID_TIKET' => $tiket->ID_TIKET,
                'NAMA_BARANG' => $a['name'],
                'HARGA_BARANG' => (string)$a['price'],
                'QTY' => $qty,
            ]);
        }

        return redirect('/booking/'.$tiket->ID_TIKET)->with('ok', 'Fasilitas berhasil disimpan');
    }
}
