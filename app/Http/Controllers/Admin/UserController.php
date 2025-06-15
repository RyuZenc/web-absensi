<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('guru')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $gurus = Guru::all(); // Untuk dropdown saat membuat user role guru
        return view('admin.users.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'guru'])], // Hanya izinkan admin atau guru
            'related_id' => 'nullable|exists:guru,id', // Hanya jika role adalah guru
        ]);

        if ($request->role === 'guru' && !$request->related_id) {
            return back()->withInput()->withErrors(['related_id' => 'Jika peran adalah guru, harus memilih guru terkait.']);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'related_id' => $request->role === 'guru' ? $request->related_id : null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $gurus = Guru::all(); // Untuk dropdown saat mengedit user role guru
        return view('admin.users.edit', compact('user', 'gurus'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'guru'])],
            'related_id' => 'nullable|exists:guru,id',
        ]);

        if ($request->role === 'guru' && !$request->related_id) {
            return back()->withInput()->withErrors(['related_id' => 'Jika peran adalah guru, harus memilih guru terkait.']);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->related_id = $request->role === 'guru' ? $request->related_id : null; // Set null jika bukan guru

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->user()->id) {
                return redirect()->route('admin.users.index')->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
            }
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            // Log the actual exception message for debugging
            Log::error('Failed to delete user: ' . $e->getMessage(), ['user_id' => $user->id]);

            return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus user. ' . $e->getMessage());
        }
    }
}
