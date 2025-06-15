<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk kemudahan tanggal
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function __construct()
    {
        // Semua method di controller ini memerlukan autentikasi
        $this->middleware('auth');

        // Hanya Admin yang bisa menambah siswa
        $this->middleware('role:admin')->only(['createSiswa', 'storeSiswa']);

        // Guru dan Admin yang bisa absensi harian
        $this->middleware('role:guru,admin')->only(['absensiHarian', 'storeAbsensi']);

        // Semua peran (admin, guru, siswa) bisa melihat daftar siswa dan rekapitulasi
        $this->middleware('role:admin,guru,siswa')->only(['index', 'rekapitulasi']);
    }

    // Menampilkan daftar siswa
    public function index()
    {
        $siswas = Siswa::orderBy('nama')->get();
        return view('absensi.index', compact('siswas'));
    }

    // Menampilkan form tambah siswa
    public function createSiswa()
    {
        return view('absensi.create_siswa');
    }

    // Menyimpan siswa baru
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:siswa,nama',
        ]);

        Siswa::create(['nama' => $request->nama]);
        return redirect()->route('absensi.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    // Menampilkan form absensi harian
    public function absensiHarian()
    {
        $tanggal_sekarang = Carbon::today()->toDateString();
        $siswas = Siswa::orderBy('nama')->get();

        // Ambil status absensi untuk hari ini
        $absensi_hari_ini = Absensi::where('tanggal', $tanggal_sekarang)
            ->pluck('status', 'siswa_id')
            ->toArray();

        // Pastikan semua siswa punya entri absensi untuk hari ini (default 'Belum Absen')
        foreach ($siswas as $siswa) {
            if (!isset($absensi_hari_ini[$siswa->id])) {
                Absensi::firstOrCreate(
                    ['siswa_id' => $siswa->id, 'tanggal' => $tanggal_sekarang],
                    ['status' => 'Belum Absen']
                );
            }
        }

        // Ambil lagi setelah memastikan semua entri ada
        $absensi_untuk_form = Absensi::where('tanggal', $tanggal_sekarang)
            ->with('siswa') // Load relasi siswa
            ->orderBy(Siswa::select('nama')->whereColumn('siswa.id', 'absensis.siswa_id')) // Order by siswa nama
            ->get();

        return view('absensi.absensi_harian', compact('absensi_untuk_form', 'tanggal_sekarang'));
    }

    // Menyimpan absensi harian
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'absensi' => 'required|array',
            'absensi.*.id' => 'required|exists:absensis,id',
            'absensi.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha,Belum Absen',
        ]);

        foreach ($request->absensi as $data) {
            $absensi = Absensi::find($data['id']);
            if ($absensi) {
                $absensi->update(['status' => $data['status']]);
            }
        }

        return redirect()->route('absensi.absensiHarian')->with('success', 'Absensi berhasil disimpan!');
    }

    // Menampilkan rekapitulasi absensi
    public function rekapitulasi()
    {
        $user = Auth::user();
        $rekap = [];

        if ($user->role === 'siswa') {
            $siswa = Siswa::where('nama', $user->name)->with('absensis')->first();
            if ($siswa) {
                $hadir = $siswa->absensis->where('status', 'Hadir')->count();
                $sakit = $siswa->absensis->where('status', 'Sakit')->count();
                $izin = $siswa->absensis->where('status', 'Izin')->count();
                $alpha = $siswa->absensis->where('status', 'Alpha')->count();

                $rekap[] = [
                    'nama' => $siswa->nama,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                ];
            }
        } else { // Admin atau Guru melihat semua
            $siswas = Siswa::with('absensis')->orderBy('nama')->get();

            foreach ($siswas as $siswa) {
                $hadir = $siswa->absensis->where('status', 'Hadir')->count();
                $sakit = $siswa->absensis->where('status', 'Sakit')->count();
                $izin = $siswa->absensis->where('status', 'Izin')->count();
                $alpha = $siswa->absensis->where('status', 'Alpha')->count();

                $rekap[] = [
                    'nama' => $siswa->nama,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                ];
            }
        }

        return view('absensi.rekapitulasi', compact('rekap'));
    }
}
