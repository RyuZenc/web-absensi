<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Selamat datang, {{ Auth::user()->name }}! Anda login sebagai {{ Auth::user()->role }}.

                    @if (Auth::user()->role === 'guru')
                        <h3 class="font-semibold text-lg mt-6">Jadwal Mengajar Hari Ini
                            ({{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }})</h3>
                        @if ($jadwalHariIni->isEmpty())
                            <p>Tidak ada jadwal mengajar hari ini.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 mt-4">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kelas</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mata Pelajaran</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Waktu</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($jadwalHariIni as $jadwal)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $jadwal->kelas->nama_kelas }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('absensi.create', $jadwal->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">Isi Absensi</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif

                    @if (Auth::user()->role === 'admin')
                        <h3 class="font-semibold text-lg mt-6">Menu Admin</h3>
                        <ul class="list-disc list-inside mt-4">
                            <li><a href="{{ route('admin.users.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen User</a></li>
                            <li><a href="{{ route('admin.siswa.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen Siswa</a></li>
                            <li><a href="{{ route('admin.kelas.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen Kelas</a></li>
                            <li><a href="{{ route('admin.guru.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen Guru</a></li>
                            <li><a href="{{ route('admin.mata-pelajaran.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen Mata Pelajaran</a></li>
                            <li><a href="{{ route('admin.jadwal.index') }}"
                                    class="text-blue-600 hover:underline">Manajemen Jadwal</a></li>
                            <li><a href="{{ route('absensi.rekap') }}" class="text-blue-600 hover:underline">Rekap
                                    Absensi</a></li>
                        </ul>
                    @endif

                    @if (Auth::user()->role === 'siswa')
                        <h3 class="font-semibold text-lg mt-6">Riwayat Absensi Anda</h3>
                        @if ($absensiSiswa->isEmpty())
                            <p>Belum ada data absensi untuk Anda.</p>
                        @else
                            <div class="overflow-x-auto mt-4">
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
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($absensiSiswa as $absensi)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->isoFormat('D MMMM YYYY') }}
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
