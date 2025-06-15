<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use Carbon\Carbon;
use App\Models\Absensi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'guru') {
            $guru = $user->guru;
            $dayOfWeek = Carbon::today()->isoFormat('dddd');
            $jadwalHariIni = Jadwal::where('guru_id', $guru->id)
                ->where('hari', $dayOfWeek)
                ->with(['kelas', 'mataPelajaran'])
                ->orderBy('jam_mulai')
                ->get();
            return view('dashboard', compact('user', 'jadwalHariIni'));
        } else if ($user->role === 'siswa') {
            $siswa = $user->siswa;
            // Ambil data absensi siswa
            $absensiSiswa = Absensi::where('siswa_id', $siswa->id)
                ->with(['jadwal.kelas', 'jadwal.mataPelajaran', 'jadwal.guru'])
                ->orderBy('tanggal_absensi', 'desc')
                ->get();
            return view('dashboard', compact('user', 'absensiSiswa'));
        } else if ($user->role === 'admin') {
            return view('dashboard', compact('user'));
        }
        return view('dashboard', compact('user'));
    }
}
