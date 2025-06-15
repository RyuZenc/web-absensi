<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

// ... (routes yang dibuat oleh Breeze, seperti /dashboard)

Route::get('/', function () {
    return view('welcome'); // Atau bisa redirect ke halaman login: return redirect('/login');
});

// Setelah login, redirect ke dashboard atau halaman absensi.index
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('absensi.index'); // Setelah login, langsung ke daftar siswa
    })->name('dashboard');

    // Grouping routes absensi di bawah middleware auth
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/siswa/create', [AbsensiController::class, 'createSiswa'])->name('createSiswa');
        Route::post('/siswa/store', [AbsensiController::class, 'storeSiswa'])->name('storeSiswa');
        Route::get('/harian', [AbsensiController::class, 'absensiHarian'])->name('absensiHarian');
        Route::post('/harian', [AbsensiController::class, 'storeAbsensi'])->name('storeAbsensi');
        Route::get('/rekapitulasi', [AbsensiController::class, 'rekapitulasi'])->name('rekapitulasi');
    });
});

require __DIR__ . '/auth.php'; // Ini adalah routes autentikasi Breeze