<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id(); // absensi_id
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
            $table->date('tanggal_absensi');
            $table->time('waktu_input'); // Waktu saat absensi diinput
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa']);
            $table->text('keterangan')->nullable();
            $table->foreignId('diinput_oleh_user_id')->constrained('users')->onDelete('cascade'); // Siapa yang input
            $table->timestamps();

            // Membuat unique constraint agar satu siswa hanya bisa diinput 1x per jadwal/tanggal
            $table->unique(['siswa_id', 'jadwal_id', 'tanggal_absensi'], 'unique_absensi_per_jadwal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
