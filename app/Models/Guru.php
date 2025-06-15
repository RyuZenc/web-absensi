<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $fillable = ['nip', 'nama_lengkap'];



    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'related_id', 'id')->where('role', 'guru');
    }
}
