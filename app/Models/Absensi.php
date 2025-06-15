<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis'; // Nama tabel di database
    protected $fillable = ['siswa_id', 'tanggal', 'status']; // Kolom yang bisa diisi

    public function siswa()
    {
        return $this->belongsTo(Siswa::class); // Satu absensi dimiliki oleh satu siswa
    }
}
