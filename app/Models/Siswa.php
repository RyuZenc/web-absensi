<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa'; // Nama tabel di database
    protected $fillable = ['nama']; // Kolom yang bisa diisi

    public function absensis()
    {
        return $this->hasMany(Absensi::class); // Satu siswa punya banyak absensi
    }
}
