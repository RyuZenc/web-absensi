<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelasXipa1 = Kelas::where('nama_kelas', 'X IPA 1')->first();

        if ($kelasXipa1) {
            Siswa::create([
                'nis' => '1001',
                'nama_lengkap' => 'Dhimas Kanjeng',
                'kelas_id' => $kelasXipa1->id,
            ]);
            Siswa::create([
                'nis' => '1002',
                'nama_lengkap' => 'Aprilia Sari',
                'kelas_id' => $kelasXipa1->id,
            ]);
            // Tambahkan siswa lainnya
        }
    }
}
