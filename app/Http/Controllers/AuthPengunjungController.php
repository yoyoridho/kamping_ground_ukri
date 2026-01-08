<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthPengunjungController extends Controller
{
    public function showRegister()
    {
        return view('pengunjung.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'NAMA_PENGUNJUNG' => 'required|string|max:100',
            'GMAIL' => 'required|email|max:30',
            'NO_HP_PENGUNJUNG' => 'nullable|string|max:20',
            'PASSWORD' => 'required|string|min:6|confirmed',
        ]);

        if (Pengunjung::where('GMAIL', $data['GMAIL'])->exists()) {
            return back()->withErrors(['GMAIL' => 'Email sudah terdaftar'])->withInput();
        }

        $pengunjung = Pengunjung::create([
            'NAMA_PENGUNJUNG' => $data['NAMA_PENGUNJUNG'],
            'GMAIL' => $data['GMAIL'],
            'NO_HP_PENGUNJUNG' => $data['NO_HP_PENGUNJUNG'] ?? null,
            'PASSWORD' => Hash::make($data['PASSWORD']),
        ]);

        // Hindari double login (pengunjung + admin dalam 1 browser/session)
        Auth::guard('pegawai')->logout();
        Auth::guard('pengunjung')->login($pengunjung);
        app(\App\Http\Controllers\PengunjungOtpController::class)->sendOtp($pengunjung);

        return redirect()->route('verify-otp');

    }

    public function showLogin()
    {
        return view('pengunjung.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'GMAIL' => 'required|email',
            'PASSWORD' => 'required|string',
        ]);

        if (Auth::guard('pengunjung')->attempt(['GMAIL' => $cred['GMAIL'], 'password' => $cred['PASSWORD']])) {
            // Hindari double login (pengunjung + admin dalam 1 browser/session)
            Auth::guard('pegawai')->logout();
            $request->session()->regenerate();
            return redirect()->intended('/booking')->with('ok', 'Login berhasil!');
        }

        return back()->withErrors(['GMAIL' => 'Email / password salah'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('pengunjung')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('ok', 'Logout berhasil!');
    }
}
