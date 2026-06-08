<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokDarahLog extends Model
{
    protected $table = 'stok_darah_log';

    protected $fillable = [
        'stok_darah_id',
        'tipe',
        'jumlah',
        'keterangan',
        'permintaan_darah_id',
    ];

    public function stokDarah(): BelongsTo
    {
        return $this->belongsTo(StokDarah::class);
    }

    public function permintaanDarah(): BelongsTo
    {
        return $this->belongsTo(PermintaanDarah::class);
    }
}
