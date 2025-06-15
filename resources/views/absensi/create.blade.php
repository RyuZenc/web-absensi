<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Isi Absensi') }} - {{ $jadwal->kelas->nama_kelas }} ({{ $jadwal->mataPelajaran->nama_mapel }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Tanggal:
                        **{{ \Carbon\Carbon::parse($tanggalSekarang)->isoFormat('dddd, D MMMM YYYY') }}**</p>
                    <p class="mb-6">Waktu: **{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}**</p>

                    @if ($absensiSudahDiisi)
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Perhatian!</p>
                            <p>Absensi untuk jadwal ini pada tanggal ini sudah diisi. Mengisi ulang akan memperbarui
                                data yang sudah ada.</p>
                        </div>
                    @endif

                    <form action="{{ route('absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                        <input type="hidden" name="tanggal_absensi" value="{{ $tanggalSekarang }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIS</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Siswa</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Keterangan (opsional)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($siswaInKelas as $index => $siswa)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->nis }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $siswa->nama_lengkap }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <select name="absensi_data[{{ $index }}][status]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="Hadir">Hadir</option>
                                                    <option value="Sakit">Sakit</option>
                                                    <option value="Izin">Izin</option>
                                                    <option value="Alfa">Alfa</option>
                                                </select>
                                                <input type="hidden"
                                                    name="absensi_data[{{ $index }}][siswa_id]"
                                                    value="{{ $siswa->id }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="text"
                                                    name="absensi_data[{{ $index }}][keterangan]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    placeholder="Contoh: Demam, Izin orang tua">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <x-primary-button type="submit">Simpan Absensi</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- absensi/create.blade.php --}}

    {{-- ... kode form dan tabel di atas ... --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                form.addEventListener('submit', function(event) {
                    let isValid = true;
                    const statusSelects = form.querySelectorAll('select[name$="[status]"]');

                    statusSelects.forEach(select => {
                        if (select.value === '') { // Memastikan ada pilihan default yang kosong/invalid
                            isValid = false;
                            select.classList.add('border-red-500'); // Tambahkan border merah
                            alert('Pastikan semua status absensi telah dipilih!');
                            event.preventDefault(); // Mencegah submit form
                        } else {
                            select.classList.remove('border-red-500'); // Hapus border merah jika valid
                        }
                    });

                    if (!isValid) {
                        event.preventDefault(); // Pastikan form tidak ter-submit jika ada yang invalid
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
