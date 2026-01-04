<?php

namespace App\Http\Controllers;

use App\Models\BarangKamping;
use App\Models\HasilTiket;
use App\Models\Pembayaran;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class BookingController extends Controller
{
    // daftar item sewa (dummy master, karena DB belum punya tabel master barang)
    private array $rentalItems = [
        ['name' => 'Tenda 2 Orang', 'price' => 50000],
        ['name' => 'Tenda 4 Orang', 'price' => 80000],
        ['name' => 'Sleeping Bag', 'price' => 20000],
        ['name' => 'Matras', 'price' => 15000],
        ['name' => 'Kompor Portable', 'price' => 30000],
    ];

    public function index()
    {
        $user = Auth::guard('pengunjung')->user();

        $bookings = Tiket::with(['tempat', 'pembayaran'])
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->orderByDesc('ID_TIKET')
            ->get();

        return view('booking.index', compact('bookings'));
    }

public function create(Request $request)
{
    $tempatList = HasilTiket::orderBy('NAMA_TEMPAT')->get();
    $rentalItems = $this->rentalItems;

    $selectedTempatId = (int) $request->query('tempat', 0);

    return view('booking.create', compact('tempatList', 'rentalItems', 'selectedTempatId'));
}

    public function store(Request $request)
    {
        $user = Auth::guard('pengunjung')->user();

        $data = $request->validate([
            'ID_TEMPAT' => 'required|integer',
            'TANGGAL_MULAI' => 'required|date',
            'TANGGAL_SELESAI' => 'required|date|after:TANGGAL_MULAI',
            'JUMLAH_ORANG' => 'required|integer|min:1',
            'addons' => 'array',
            'addons.*.name' => 'string',
            'addons.*.price' => 'numeric',
            'addons.*.qty' => 'integer|min:0',
        ]);

        $tempat = HasilTiket::findOrFail($data['ID_TEMPAT']);

        // Validasi "tempat tersedia"
        if (strtoupper((string)$tempat->STATUS) !== 'TERSEDIA') {
            return back()->withErrors(['ID_TEMPAT' => 'Tempat tidak tersedia'])->withInput();
        }

        // Validasi konflik tanggal (cek booking yang sudah LUNAS di tempat yang sama)
        $mulai = Carbon::parse($data['TANGGAL_MULAI']);
        $selesai = Carbon::parse($data['TANGGAL_SELESAI']);

        $conflict = Tiket::where('ID_TEMPAT', $tempat->ID_TEMPAT)
            ->whereHas('pembayaran', function ($q) {
                $q->where('STATUS_BAYAR', 'LUNAS');
            })
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('TANGGAL_MULAI', [$mulai->toDateString(), $selesai->toDateString()])
                  ->orWhereBetween('TANGGAL_SELESAI', [$mulai->toDateString(), $selesai->toDateString()])
                  ->orWhere(function ($qq) use ($mulai, $selesai) {
                      $qq->where('TANGGAL_MULAI', '<=', $mulai->toDateString())
                         ->where('TANGGAL_SELESAI', '>=', $selesai->toDateString());
                  });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['TANGGAL_MULAI' => 'Tanggal bentrok: tempat sudah dibooking'])->withInput();
        }

        // Buat booking (tiket)
        $tiket = Tiket::create([
            'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
            'ID_TEMPAT' => $tempat->ID_TEMPAT,
            'TANGGAL_MULAI' => $mulai->toDateString(),
            'TANGGAL_SELESAI' => $selesai->toDateString(),
            'JUMLAH_ORANG' => $data['JUMLAH_ORANG'],
        ]);

        // Simpan addon jika ada
        $addons = $request->input('addons', []);
        foreach ($addons as $a) {
            if (!isset($a['name'], $a['price'], $a['qty'])) continue;

            BarangKamping::create([
                'ID_PENGUNJUNG' => $user->ID_PENGUNJUNG,
                'ID_TEMPAT' => $tempat->ID_TEMPAT,
                'ID_TIKET' => $tiket->ID_TIKET,
                'NAMA_BARANG' => $a['name'],
                'HARGA_BARANG' => (string)$a['price'], // DB: varchar(25)
                'QTY' => (int)$a['qty'],
            ]);
        }

        // Redirect ke detail booking
        return redirect('/booking/'.$tiket->ID_TIKET.'/fasilitas')->with('ok', 'Booking dibuat. Silakan pilih fasilitas (opsional).');

    }

    public function show($id)
    {
        $user = Auth::guard('pengunjung')->user();

        $tiket = Tiket::with(['tempat', 'addons', 'pembayaran'])
            ->where('ID_TIKET', $id)
            ->where('ID_PENGUNJUNG', $user->ID_PENGUNJUNG)
            ->firstOrFail();

        // hitung total untuk ditampilkan
        $mulai = Carbon::parse($tiket->TANGGAL_MULAI);
        $selesai = Carbon::parse($tiket->TANGGAL_SELESAI);
        $nights = max(1, $mulai->diffInDays($selesai));

        $hargaTempat = (float)$tiket->tempat->HARGA_PER_MALAM;
        $totalTempat = $hargaTempat * $nights;

        $totalAddon = 0;
        foreach ($tiket->addons as $ad) {
            $price = (float)$ad->HARGA_BARANG;
            $qty = (int)($ad->QTY ?? 1);
            // asumsi addon dihitung per malam juga:
            $totalAddon += ($price * $qty * $nights);
        }

        $grandTotal = $totalTempat + $totalAddon;

        return view('booking.show', compact('tiket', 'nights', 'totalTempat', 'totalAddon', 'grandTotal'));
    }
}
