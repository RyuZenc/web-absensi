@extends('absensi.layout')

@section('content')
    <h1>Absensi Harian - {{ $tanggal_sekarang }}</h1>

    @if ($absensi_untuk_form->isEmpty())
        <p>Belum ada siswa yang terdaftar. Silakan <a href="{{ route('absensi.createSiswa') }}">tambah siswa</a> terlebih
            dahulu.</p>
    @else
        <form action="{{ route('absensi.storeAbsensi') }}" method="POST">
            @csrf
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensi_untuk_form as $index => $absensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $absensi->siswa->nama }}</td>
                            <td>
                                <input type="hidden" name="absensi[{{ $index }}][id]" value="{{ $absensi->id }}">
                                <select name="absensi[{{ $index }}][status]">
                                    <option value="Hadir" {{ $absensi->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="Sakit" {{ $absensi->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Izin" {{ $absensi->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="Alpha" {{ $absensi->status == 'Alpha' ? 'selected' : '' }}>Alpha
                                    </option>
                                    <option value="Belum Absen" {{ $absensi->status == 'Belum Absen' ? 'selected' : '' }}>
                                        Belum Absen</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit">Simpan Absensi</button>
        </form>
    @endif
@endsection
