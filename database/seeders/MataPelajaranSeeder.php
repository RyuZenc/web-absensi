<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        MataPelajaran::create(['nama_mapel' => 'Matematika']);
        MataPelajaran::create(['nama_mapel' => 'Bahasa Indonesia']);
        MataPelajaran::create(['nama_mapel' => 'Fisika']);
        MataPelajaran::create(['nama_mapel' => 'Kimia']);
    }
}
