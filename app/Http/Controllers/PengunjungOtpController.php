<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailOtpMail;
use Illuminate\Http\Request;

class PengunjungOtpController extends Controller
{
    public function showForm()
    {
        return view('auth.verify-otp'); // yang kamu sudah punya
    }

    public function sendOtp(Pengunjung $pengunjung)
    {
        $otp = (string) random_int(100000, 999999);

        $pengunjung->forceFill([
            'email_otp' => $otp,
            'email_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($pengunjung->GMAIL)->send(new EmailOtpMail($otp));
    }

    public function resend()
   {
    $id = Auth::guard('pengunjung')->id();
    if (!$id) abort(401);

    $id = Auth::guard('pengunjung')->id();
    $pengunjung = Pengunjung::findOrFail($id);

    $this->sendOtp($pengunjung);

    return back()->with('success', 'OTP dikirim ulang.');
    }


    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string']);

        $pengunjung = Auth::guard('pengunjung')->user();
        if (!$pengunjung) abort(401);

        if (!$pengunjung->email_otp || !$pengunjung->email_otp_expires_at) {
            return back()->withErrors(['otp' => 'OTP belum dikirim. Klik kirim ulang.']);
        }

        if (now()->gt($pengunjung->email_otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP expired. Klik kirim ulang.']);
        }

        if ($request->otp !== $pengunjung->email_otp) {
            return back()->withErrors(['otp' => 'OTP salah.']);
        }

        $pengunjung->email_verified_at = now();
        $pengunjung->email_otp = null;
        $pengunjung->email_otp_expires_at = null;


        return redirect('/booking')->with('ok', 'Email berhasil diverifikasi.');
    }
}
