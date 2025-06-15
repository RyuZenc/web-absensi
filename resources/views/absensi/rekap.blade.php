<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekap Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('absensi.rekap') }}" method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="kelas_id" class="block text-sm font-medium text-gray-700">Pilih Kelas</label>
                            <select name="kelas_id" id="kelas_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="mapel_id" class="block text-sm font-medium text-gray-700">Pilih Mata
                                Pelajaran</label>
                            <select name="mapel_id" id="mapel_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach ($mapel as $m)
                                    <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>
                                        {{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal
                                Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                value="{{ old('tanggal_mulai', request('tanggal_mulai')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal
                                Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                value="{{ old('tanggal_selesai', request('tanggal_selesai')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <x-primary-button type="submit">Filter</x-primary-button>
                        </div>

                        <div class="ml-auto">
                            <a href="{{ route('admin.absensi.export-excel', request()->query()) }}"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Export ke Excel
                            </a>
                        </div>
                    </form>

                    @if ($absensiData->isEmpty())
                        <p>Tidak ada data absensi ditemukan untuk filter yang dipilih.</p>
                    @else
                        @foreach ($absensiData as $namaSiswa => $absensisPerSiswa)
                            <h3 class="font-semibold text-lg mt-6 mb-2">Rekap Absensi Siswa: {{ $namaSiswa }}
                                ({{ $absensisPerSiswa->first()->siswa->kelas->nama_kelas ?? 'N/A' }})
                            </h3>
                            <div class="overflow-x-auto mb-6">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mata Pelajaran</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Waktu</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Keterangan</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Diinput Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($absensisPerSiswa as $absensi)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->isoFormat('D MMM YYYY') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $absensi->jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($absensi->jadwal->jam_mulai)->format('H:i') ?? 'N/A' }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($absensi->jadwal->jam_selesai)->format('H:i') ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if ($absensi->status == 'Hadir') bg-green-100 text-green-800
                                                        @elseif($absensi->status == 'Sakit') bg-yellow-100 text-yellow-800
                                                        @elseif($absensi->status == 'Izin') bg-blue-100 text-blue-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ $absensi->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $absensi->keterangan ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $absensi->inputBy->name ?? 'Sistem' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
