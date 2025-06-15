<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = MataPelajaran::latest()->paginate(10);
        return view('admin.mata-pelajaran.index', compact('mataPelajaran'));
    }

    public function create()
    {
        return view('admin.mata-pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255|unique:mata_pelajaran,nama_mapel',
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mata_pelajaran) // Laravel match 'mata_pelajaran' dari route
    {
        return view('admin.mata-pelajaran.show', compact('mata_pelajaran'));
    }

    public function edit(MataPelajaran $mata_pelajaran)
    {
        return view('admin.mata-pelajaran.edit', compact('mata_pelajaran'));
    }

    public function update(Request $request, MataPelajaran $mata_pelajaran)
    {
        $request->validate([
            'nama_mapel' => ['required', 'string', 'max:255', Rule::unique('mata_pelajaran', 'nama_mapel')->ignore($mata_pelajaran->id)],
        ]);

        $mata_pelajaran->update($request->all());

        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mata_pelajaran)
    {
        try {
            $mata_pelajaran->delete();
            return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Data mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.mata-pelajaran.index')->with('error', 'Gagal menghapus mata pelajaran. Mungkin ada jadwal terkait.');
        }
    }
}
