<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        Kelas::create(['nama_kelas' => 'X IPA 1']);
        Kelas::create(['nama_kelas' => 'X IPA 2']);
        Kelas::create(['nama_kelas' => 'XI IPS 1']);
        Kelas::create(['nama_kelas' => 'XII IPS 1']);
    }
}
