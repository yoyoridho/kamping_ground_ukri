<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthPegawaiController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'EMAIL_PEGAWAI' => 'required|email',
            'PASSWORD_PEGAWAI' => 'required|string',
        ]);

        if (Auth::guard('pegawai')->attempt(['EMAIL_PEGAWAI' => $cred['EMAIL_PEGAWAI'], 'password' => $cred['PASSWORD_PEGAWAI']])) {
            $request->session()->regenerate();
            return redirect('/admin/tempat')->with('ok', 'Login admin berhasil!');
        }

        return back()->withErrors(['EMAIL_PEGAWAI' => 'Email / password admin salah'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login')->with('ok', 'Logout admin berhasil!');
    }
}
