<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $q = Pembayaran::with(['tiket.pengunjung', 'tiket.tempat'])
            ->orderByDesc('ID_PEMBAYARAN');

        if ($request->filled('from')) {
            $q->whereDate('TANGGAL_PEMBAYARAN', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('TANGGAL_PEMBAYARAN', '<=', $request->input('to'));
        }

        if ($request->filled('status')) {
            $q->where('STATUS_BAYAR', $request->input('status'));
        }

        $rows = $q->get();

        return view('admin.report.index', compact('rows'));
    }
}
