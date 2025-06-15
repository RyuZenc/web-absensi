@extends('absensi.layout')

@section('content')
    <h1>Tambah Siswa Baru</h1>

    <form action="{{ route('absensi.storeSiswa') }}" method="POST">
        @csrf
        <label for="nama">Nama Siswa:</label>
        <input type="text" id="nama" name="nama" required>
        <button type="submit">Tambah Siswa</button>
    </form>
@endsection
