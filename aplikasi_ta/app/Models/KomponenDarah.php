<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KomponenDarah extends Model
{
    protected $table = 'komponen_darah';

    protected $fillable = [
        'kode',
        'nama_lengkap',
    ];

    public function permintaanDarah(): HasMany
    {
        return $this->hasMany(PermintaanDarah::class);
    }

    public function stokDarah(): HasMany
    {
        return $this->hasMany(StokDarah::class);
    }
}
