<?php

namespace App\Http\Controllers;

use App\Models\HasilTiket;
use Illuminate\Support\Facades\Auth;
use App\Models\Fasilitas;

class DashboardController extends Controller
{
    public function index()
    {
        // Tempat yang dipost admin
        $tempatList = HasilTiket::orderByDesc('ID_TEMPAT')->get();

        $fasilitasList = Fasilitas::where('STATUS', 'AKTIF')
        ->orderBy('NAMA_FASILITAS')
        ->get()
        ->map(fn($x) => ['name' => $x->NAMA_FASILITAS, 'price' => (int)$x->HARGA_FASILITAS])
        ->toArray();


        $isLoggedIn = Auth::guard('pengunjung')->check();

        // Tombol booking: login dulu kalau belum login
        $bookingUrl = '/booking/create';
        $myBookingUrl = '/booking';

        return view('dashboard', compact('tempatList', 'fasilitasList', 'bookingUrl', 'myBookingUrl', 'isLoggedIn'));
    }
}
