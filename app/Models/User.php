<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahkan kolom role
        'related_id', // Tambahkan kolom related_id
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke model Guru jika user ini adalah guru
    public function guru()
    {
        return $this->hasOne(Guru::class, 'id', 'related_id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id', 'related_id');
    }
}
