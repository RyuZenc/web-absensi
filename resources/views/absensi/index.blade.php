@extends('absensi.layout')

@section('content')
    <h1>Daftar Siswa</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Belum ada siswa yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
