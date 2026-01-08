<?php

namespace App\Http\Controllers;

use App\Models\HasilTiket;
use Illuminate\Support\Facades\Auth;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // kalau user sedang memakai filter tempat, paksa tab=tempat
        if ($request->hasAny(['q','status','sort']) && $request->query('tab') !== 'tempat') {
        $qs = $request->query();
        $qs['tab'] = 'tempat';
        return redirect('/?' . http_build_query($qs));
    }


        $tempatList = HasilTiket::orderByDesc('ID_TEMPAT')->get();

        $fasilitasList = Fasilitas::where('STATUS', 'AKTIF')
            ->where('STOK','>',0)
            ->orderBy('NAMA_FASILITAS')
            ->get()
            ->map(fn($x) => ['name' => $x->NAMA_FASILITAS, 'price' => (int)$x->HARGA_FASILITAS])
            ->toArray();

        $isLoggedIn = Auth::guard('pengunjung')->check();

        $bookingUrl = '/booking/create';
        $myBookingUrl = '/booking';

        return view('dashboard', compact('tempatList', 'fasilitasList', 'bookingUrl', 'myBookingUrl', 'isLoggedIn'));
    }
}
