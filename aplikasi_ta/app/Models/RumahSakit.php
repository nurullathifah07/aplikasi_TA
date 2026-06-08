<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RumahSakit extends Model
{
    protected $table = 'rumah_sakit';

    protected $fillable = [
        'nama',
        'alamat',
    ];

    public function permintaanDarah(): HasMany
    {
        return $this->hasMany(PermintaanDarah::class);
    }
}
