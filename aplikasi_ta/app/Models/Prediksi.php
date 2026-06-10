<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediksi extends Model
{
    protected $table = 'prediksi';

    protected $fillable = [
        'tanggal_prediksi',
        'golongan_darah',
        'komponen_darah_id',
        'tanggal_target',
        'nilai_prediksi',
        'alpha',
        'beta',
        'rmse',
        'mape',
        'mae',
        'rasio_split',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_prediksi' => 'date',
            'tanggal_target' => 'date',
            'nilai_prediksi' => 'decimal:2',
            'alpha' => 'decimal:4',
            'beta' => 'decimal:4',
            'rmse' => 'decimal:4',
            'mape' => 'decimal:4',
            'mae' => 'decimal:4',
        ];
    }

    public function komponenDarah(): BelongsTo
    {
        return $this->belongsTo(KomponenDarah::class);
    }
}
