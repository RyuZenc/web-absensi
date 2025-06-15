<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import untuk hashing password
use Illuminate\Validation\Rule; // Import untuk validasi Rule

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->latest()->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|string|max:255|unique:guru,nip',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // Email untuk akun user guru
            'password' => 'required|string|min:8|confirmed', // Password untuk akun user guru
        ]);

        $guru = Guru::create([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        // Buat akun User terkait
        User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'related_id' => $guru->id,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akun user berhasil ditambahkan.');
    }

    public function show(Guru $guru)
    {
        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip' => ['nullable', 'string', 'max:255', Rule::unique('guru', 'nip')->ignore($guru->id)],
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guru->user->id ?? null)], // Email unik kecuali miliknya sendiri
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat update
        ]);

        $guru->update([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        // Update akun User terkait
        $user = $guru->user; // Dapatkan user terkait

        if ($user) {
            $user->email = $request->email;
            $user->name = $request->nama_lengkap; // Update nama user sesuai nama guru
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        } else {
            // Jika user belum ada (misal: data guru lama), buat baru
            User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password'), // Beri default jika kosong
                'role' => 'guru',
                'related_id' => $guru->id,
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akun user berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        try {
            if ($guru->user) {
                $guru->user->delete(); // Hapus akun user terkait
            }
            $guru->delete(); // Hapus data guru
            return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akun user berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.guru.index')->with('error', 'Gagal menghapus guru. Mungkin ada jadwal terkait.');
        }
    }
}
