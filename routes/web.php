<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthPengunjungController;
use App\Http\Controllers\AuthPegawaiController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\TempatController;
use App\Http\Controllers\Admin\ReportController;

// Landing
Route::get('/', fn() => redirect('/booking'));

// Pengunjung auth
Route::get('/register', [AuthPengunjungController::class, 'showRegister']);
Route::post('/register', [AuthPengunjungController::class, 'register']);
Route::get('/login', [AuthPengunjungController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthPengunjungController::class, 'login']);
Route::post('/logout', [AuthPengunjungController::class, 'logout']);

// Booking (pengunjung)
Route::middleware('auth:pengunjung')->group(function () {
    Route::get('/booking', [BookingController::class, 'index']);
    Route::get('/booking/create', [BookingController::class, 'create']);
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);

    // Payment dummy
    Route::get('/booking/{id}/pay', [PaymentController::class, 'createForBooking']);
    Route::get('/payment/{id}/pay', [PaymentController::class, 'payPage']);
    Route::post('/payment/{id}/simulate', [PaymentController::class, 'simulate']);
});

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

    Route::get('/report', [ReportController::class, 'index']);
});

