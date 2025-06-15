<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KelasSeeder::class,
            MataPelajaranSeeder::class,
            GuruSeeder::class, // GuruSeeder harus dipanggil setelah UserSeeder jika terkait
            JadwalSeeder::class, // JadwalSeeder setelah Kelas, Mapel, Guru
            SiswaSeeder::class, // SiswaSeeder setelah Kelas
        ]);
    }
}
