<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthPengunjungController;
use App\Http\Controllers\AuthPegawaiController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\TempatController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingFasilitasController;
use App\Http\Controllers\BookingWizardController;
use App\Http\Controllers\Admin\FasilitasController as AdminFasilitasController;
use App\Http\Controllers\Admin\TiketScanController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PengunjungOtpController;

Route::middleware('auth:pengunjung')->group(function () {
    Route::get('/verify-otp', [PengunjungOtpController::class, 'showForm'])->name('verify-otp');
    Route::post('/verify-otp', [PengunjungOtpController::class, 'verify'])->name('verify-otp.submit');
    Route::post('/resend-otp', [PengunjungOtpController::class, 'resend'])->name('resend-otp');
});


// Landing
Route::get('/', [DashboardController::class, 'index']);

// Pengunjung auth
Route::get('/register', [AuthPengunjungController::class, 'showRegister']);
Route::post('/register', [AuthPengunjungController::class, 'register']);
Route::get('/login', [AuthPengunjungController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthPengunjungController::class, 'login']);
Route::post('/logout', [AuthPengunjungController::class, 'logout']);

// Booking (pengunjung)
Route::middleware('auth:pengunjung')->group(function () {
    Route::get('/booking/wizard', [BookingWizardController::class, 'step1'])->name('booking.wizard.step1');
    Route::post('/booking/wizard/step1', [BookingWizardController::class, 'postStep1'])->name('booking.wizard.postStep1');

    Route::get('/booking/wizard/fasilitas', [BookingWizardController::class, 'step2'])->name('booking.wizard.step2');
    Route::post('/booking/wizard/finish', [BookingWizardController::class, 'finish'])->name('booking.wizard.finish');
    Route::get('/booking/{id}/fasilitas', [BookingFasilitasController::class, 'edit']);
    Route::post('/booking/{id}/fasilitas', [BookingFasilitasController::class, 'update']);
    Route::get('/booking', [BookingController::class, 'index']);
    Route::get('/booking/create', [BookingController::class, 'create']);
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);

    // Payment (Midtrans Snap)
    Route::get('/booking/{id}/pay', [PaymentController::class, 'createForBooking']);
    Route::get('/payment/{id}/pay', [PaymentController::class, 'payPage']);
    Route::post('/payment/{id}/result', [PaymentController::class, 'storeResult']);
    // optional: simulasi tanpa midtrans
    Route::post('/payment/{id}/simulate', [PaymentController::class, 'simulate']);
});

// Webhook Midtrans (tidak pakai auth)
Route::post('/midtrans/notification', [PaymentController::class, 'notification']);


// Admin auth
Route::get('/admin/login', [AuthPegawaiController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthPegawaiController::class, 'login']);
Route::post('/admin/logout', [AuthPegawaiController::class, 'logout']);

// Admin pages
Route::prefix('admin')->middleware('auth:pegawai')->group(function () {
    Route::get('/tempat', [TempatController::class, 'index']);
    Route::get('/tempat/create', [TempatController::class, 'create']);
    Route::post('/tempat', [TempatController::class, 'store']);
    Route::get('/tempat/{id}/edit', [TempatController::class, 'edit']);
    Route::post('/tempat/{id}', [TempatController::class, 'update']);
    Route::get('/fasilitas', [AdminFasilitasController::class, 'index']);
    Route::get('/fasilitas/create', [AdminFasilitasController::class, 'create']);
    Route::post('/fasilitas', [AdminFasilitasController::class, 'store']);
    Route::get('/fasilitas/{id}/edit', [AdminFasilitasController::class, 'edit']);
    Route::post('/fasilitas/{id}', [AdminFasilitasController::class, 'update']);
    Route::post('/fasilitas/{id}/delete', [AdminFasilitasController::class, 'destroy']);
    Route::get('/report', [ReportController::class, 'index']);
    Route::get('/scan/{token}', [TiketScanController::class, 'show'])->name('admin.scan.show');
    Route::post('/scan/{token}/checkin', [TiketScanController::class, 'checkin'])->name('admin.scan.checkin');
});

