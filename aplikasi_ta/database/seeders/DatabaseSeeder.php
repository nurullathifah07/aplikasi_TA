<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\KomponenDarah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin default
        Akun::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'no_telpon' => '08123456789',
                'email' => 'admin@pmi-tanahlaut.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Buat komponen darah default
        KomponenDarah::firstOrCreate(
            ['kode' => 'WB'],
            ['nama_lengkap' => 'Whole Blood']
        );

        KomponenDarah::firstOrCreate(
            ['kode' => 'PRC'],
            ['nama_lengkap' => 'Packed Red Cell']
        );
    }
}
