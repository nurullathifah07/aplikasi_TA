<?php

namespace App\Imports;

use App\Models\PermintaanDarah;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PermintaanDarahImport implements ToArray, WithHeadingRow
{
    private $errors = [];
    private $imported = 0;

    public function array(array $rows)
    {
        // Validasi header
        $expectedHeaders = ['tanggal', 'id_rs', 'golongan_darah', 'komponen', 'jumlah', 'status'];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Baris 1 = header, data mulai baris 2

            // Validasi data per baris
            if (empty($row['tanggal']) || empty($row['id_rs']) || empty($row['golongan_darah']) || empty($row['komponen']) || empty($row['jumlah'])) {
                $this->errors[] = "Baris {$rowNumber}: Data tidak lengkap.";
                continue;
            }

            // Validasi golongan darah
            if (!in_array(strtoupper($row['golongan_darah']), ['A', 'B', 'AB', 'O'])) {
                $this->errors[] = "Baris {$rowNumber}: Golongan darah tidak valid ({$row['golongan_darah']}).";
                continue;
            }

            // Cari komponen darah berdasarkan kode
            $komponen = \App\Models\KomponenDarah::where('kode', strtoupper($row['komponen']))->first();
            if (!$komponen) {
                $this->errors[] = "Baris {$rowNumber}: Komponen darah '{$row['komponen']}' tidak ditemukan.";
                continue;
            }

            // Cek RS ada
            $rs = \App\Models\RumahSakit::find($row['id_rs']);
            if (!$rs) {
                $this->errors[] = "Baris {$rowNumber}: Rumah Sakit dengan ID {$row['id_rs']} tidak ditemukan.";
                continue;
            }

            // Validasi status
            $status = strtolower($row['status'] ?? 'pending');
            if (!in_array($status, ['pending', 'terpenuhi', 'ditolak'])) {
                $status = 'pending';
            }

            // Parse tanggal (support format Excel numeric atau string)
            $tanggal = $this->parseTanggal($row['tanggal']);
            if (!$tanggal) {
                $this->errors[] = "Baris {$rowNumber}: Format tanggal tidak valid ({$row['tanggal']}).";
                continue;
            }

            // Simpan data
            PermintaanDarah::create([
                'rumah_sakit_id' => $row['id_rs'],
                'tanggal' => $tanggal,
                'golongan_darah' => strtoupper($row['golongan_darah']),
                'komponen_darah_id' => $komponen->id,
                'jumlah' => (int) $row['jumlah'],
                'status' => $status,
            ]);

            $this->imported++;
        }
    }

    // Parse tanggal dari berbagai format (Excel numeric, Y-m-d, d/m/Y)
    private function parseTanggal($value)
    {
        if (is_numeric($value)) {
            // Excel serial date number
            return \Carbon\Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($value - 25569) * 86400))->format('Y-m-d');
        }

        // Coba format Y-m-d
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            // Coba format d/m/Y
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }
}
