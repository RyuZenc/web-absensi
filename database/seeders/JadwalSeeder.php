<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $kelasXipa1 = Kelas::where('nama_kelas', 'X IPA 1')->first();
        $matematika = MataPelajaran::where('nama_mapel', 'Matematika')->first();
        $fisika = MataPelajaran::where('nama_mapel', 'Fisika')->first();
        $budi = Guru::where('nama_lengkap', 'Budi Santoso')->first();
        $ani = Guru::where('nama_lengkap', 'Ani Wijaya')->first();

        if ($kelasXipa1 && $matematika && $budi) {
            Jadwal::create([
                'kelas_id' => $kelasXipa1->id,
                'mapel_id' => $matematika->id,
                'guru_id' => $budi->id,
                'hari' => 'Senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
                'tahun_ajaran' => '2024/2025',
            ]);
        }
        if ($kelasXipa1 && $fisika && $ani) {
            Jadwal::create([
                'kelas_id' => $kelasXipa1->id,
                'mapel_id' => $fisika->id,
                'guru_id' => $ani->id,
                'hari' => 'Senin',
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '10:00:00',
                'tahun_ajaran' => '2024/2025',
            ]);
        }
        // Tambahkan jadwal lainnya sesuai kebutuhan
    }
}
