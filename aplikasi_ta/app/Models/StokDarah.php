<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokDarah extends Model
{
    protected $table = 'stok_darah';

    protected $fillable = [
        'golongan_darah',
        'komponen_darah_id',
        'jumlah',
    ];

    public function komponenDarah(): BelongsTo
    {
        return $this->belongsTo(KomponenDarah::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(StokDarahLog::class);
    }
}
