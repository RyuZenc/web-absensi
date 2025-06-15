<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guru; // Import model Guru

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user Admin
        User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@yadika.com',
            'password' => bcrypt('yadika_password96'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Buat contoh Guru dan User terkait
        $guru1 = Guru::create([
            'nip' => 'GTEST001',
            'nama_lengkap' => 'Dhxms',
        ]);
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@guru.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'related_id' => $guru1->id,
            'email_verified_at' => now(),
        ]);

        $guru2 = Guru::create([
            'nip' => 'GURU002',
            'nama_lengkap' => 'Ani Wijaya',
        ]);
        User::create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@sekolah.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'related_id' => $guru2->id,
            'email_verified_at' => now(),
        ]);
    }
}
