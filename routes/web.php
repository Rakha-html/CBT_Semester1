<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\TugasController;
use App\Http\Controllers\Admin\BuktiLaporanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Staff\TugasSayaController;
use Illuminate\Support\Facades\Route;

// ================================================================
// GUEST ROUTES
// ================================================================
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ================================================================
// AUTHENTICATED ROUTES
// ================================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ============================================================
    // PANEL KETUA SPMB (Admin)
    // ============================================================
    Route::middleware('role:ketua_spmb')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::get('/monitoring/{tugas}/review', [MonitoringController::class, 'review'])->name('review');
        Route::post('/verifikasi/{pengumpulan}', [MonitoringController::class, 'verifikasi'])->name('verifikasi');
        Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/bukti-laporan', [BuktiLaporanController::class, 'index'])->name('bukti-laporan');
        Route::post('/bukti-laporan/{pengumpulan}/verifikasi', [BuktiLaporanController::class, 'verifikasi'])->name('bukti-laporan.verifikasi');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    });

    // ============================================================
    // PANEL PANITIA / STAFF (PIC)
    // ============================================================
    Route::middleware('role:panitia')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/tugas-saya', [TugasSayaController::class, 'index'])->name('tugas-saya');
        Route::post('/tugas/{tugas}/update-status', [TugasSayaController::class, 'updateStatus'])->name('update-status');
        Route::get('/tugas/{tugas}/lapor', [TugasSayaController::class, 'lapor'])->name('lapor');
        Route::post('/tugas/{tugas}/lapor', [TugasSayaController::class, 'submitLaporan'])->name('submit-laporan');
    });
});
