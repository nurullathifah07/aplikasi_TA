<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Akun extends Authenticatable
{
    protected $table = 'akun';

    protected $fillable = [
        'foto',
        'nama',
        'no_telpon',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [];
    }
}
