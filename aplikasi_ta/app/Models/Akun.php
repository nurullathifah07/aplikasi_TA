<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    protected $table = 'akun';

    protected $fillable = [
        'foto',
        'username',
        'email',
        'password',
        'role'
    ];
}
