<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\JadwalController;

// Route untuk halaman utama (setelah login)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route khusus Guru
    Route::middleware(['role:guru'])->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/create/{jadwal_id}', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    });
    //hey

    // Route khusus Admin
    Route::middleware(['role:admin'])->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('mata-pelajaran', MataPelajaranController::class);
        Route::resource('jadwal', JadwalController::class);
        Route::resource('admin/users', UserController::class);
        Route::resource('admin/siswa', SiswaController::class);
        Route::resource('admin/kelas', KelasController::class);
        Route::resource('admin/guru', GuruController::class);
        Route::resource('admin/mata-pelajaran', MataPelajaranController::class);
        Route::resource('admin/jadwal', JadwalController::class);

        Route::get('absensi/export-excel', [AbsensiController::class, 'exportExcel'])->name('absensi.export-excel');
    });
});

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';
