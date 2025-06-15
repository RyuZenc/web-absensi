@extends('absensi.layout')

@section('content')
    <h1>Rekapitulasi Absensi Siswa</h1>

    @if (empty($rekap))
        <p>Belum ada data absensi untuk direkapitulasi.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alpha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekap as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $data['nama'] }}</td>
                        <td>{{ $data['hadir'] }}</td>
                        <td>{{ $data['sakit'] }}</td>
                        <td>{{ $data['izin'] }}</td>
                        <td>{{ $data['alpha'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
