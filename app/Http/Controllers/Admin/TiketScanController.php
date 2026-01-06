<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TiketScanController extends Controller
{
    public function show($token)
    {
        $tiket = Tiket::with(['tempat', 'addons'])
            ->where('QR_TOKEN', $token)
            ->first();

        return view('admin.scan.verify', compact('tiket', 'token'));
    }

    public function checkin(Request $request, $token)
    {
        $tiket = Tiket::where('QR_TOKEN', $token)->firstOrFail();

        if (strtoupper((string)$tiket->STATUS_PEMBAYARAN) !== 'TERBAYAR') {
            return back()->with('err', 'Tiket belum terbayar.');
        }

        if (strtoupper((string)$tiket->CHECKIN_STATUS) === 'SUDAH') {
            return back()->with('err', 'Tiket sudah pernah check-in.');
        }

        $pegawai = auth('pegawai')->user();

        $tiket->CHECKIN_STATUS = 'SUDAH';
        $tiket->CHECKIN_AT = Carbon::now();

        if (isset($pegawai->ID_PEGAWAI)) {
            $tiket->CHECKIN_BY = $pegawai->ID_PEGAWAI;
        }

        $tiket->save();

        return back()->with('ok', 'Check-in berhasil. Tiket valid dan sudah digunakan.');
    }
}
