<?php

namespace App\Http\Controllers;

use App\Mail\EmailOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class OtpController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp');
    }

    /**
 * @param \App\Models\User $user
 */
public function sendOtp(User $user): void
{
    /** @var \App\Models\User $user */
    $otp = (string) random_int(100000, 999999);

    $user->email_otp = $otp;
    $user->email_otp_expires_at = now()->addMinutes(10);
    $user->save();

    Mail::to($user->email)->send(new EmailOtpMail($otp));
}

    public function resend(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        abort(401);
    }

    $this->sendOtp($user);

    return back()->with('success', 'OTP sudah dikirim ulang.');
}


    public function verify(Request $request)
{
    $request->validate([
        'otp' => 'required|string'
    ]);

    $user = Auth::user();

    if (!$user) {
        abort(401);
    }

    if (
        $user->email_otp !== $request->otp ||
        now()->greaterThan($user->email_otp_expires_at)
    ) {
        return back()->withErrors(['otp' => 'OTP salah atau sudah kadaluarsa']);
    }

    $user->email_verified_at = now();
    $user->email_otp = null;
    $user->email_otp_expires_at = null;

    return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi');
}

}
