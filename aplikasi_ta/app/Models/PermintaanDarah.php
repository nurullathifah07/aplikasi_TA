<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanDarah extends Model
{
    protected $table = 'permintaan_darah';

    protected $fillable = [
        'rumah_sakit_id',
        'tanggal',
        'golongan_darah',
        'komponen_darah_id',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function rumahSakit(): BelongsTo
    {
        return $this->belongsTo(RumahSakit::class);
    }

    public function komponenDarah(): BelongsTo
    {
        return $this->belongsTo(KomponenDarah::class);
    }

    public function stokDarahLogs(): HasMany
    {
        return $this->hasMany(StokDarahLog::class);
    }
}
