<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplatePermintaanDarahExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['tanggal', 'id_rs', 'golongan_darah', 'komponen', 'jumlah'];
    }

    public function array(): array
    {
        // Contoh data sebagai panduan
        return [
            ['2026-06-01', 1, 'A', 'WB', 4],
            ['2026-06-01', 2, 'B', 'PRC', 3],
        ];
    }
}
