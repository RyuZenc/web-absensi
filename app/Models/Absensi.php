<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';
    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal_absensi',
        'waktu_input',
        'status',
        'keterangan',
        'diinput_oleh_user_id'
    ];

    protected $casts = [
        'tanggal_absensi' => 'date',
        'waktu_input' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'diinput_oleh_user_id');
    }
}
