<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with('kelas')->latest()->paginate(10);
        return view('admin.siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:255|unique:siswa,nis',
            'nama_lengkap' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            // Tambahkan validasi untuk email user siswa
            'email' => 'required|string|email|max:255|unique:users,email', // Email untuk akun user
            'password' => 'required|string|min:8|confirmed', // Password untuk akun user, butuh konfirmasi
        ]);

        $siswa = Siswa::create([
            'nis' => $request->nis,
            'nama_lengkap' => $request->nama_lengkap,
            'kelas_id' => $request->kelas_id,
        ]);

        User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // Atur role sebagai 'siswa'
            'related_id' => $siswa->id, // Hubungkan ke ID siswa yang baru dibuat
            'email_verified_at' => now(), // Opsional: verifikasi email langsung
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa dan akun user berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }


    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|string|max:255|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($siswa->user ? $siswa->user->id : 'NULL'),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $siswa->update($request->except(['email', 'password', 'password_confirmation']));

        $user = $siswa->user;

        if ($user) {
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->name = $request->nama_lengkap;
            $user->save();
        } else {

            User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'default_password'),
                'role' => 'siswa',
                'related_id' => $siswa->id,
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa dan akun user berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        try {
            $siswa->delete();
            return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa.index')->with('error', 'Gagal menghapus siswa. Mungkin ada data terkait.');
        }
    }
}
