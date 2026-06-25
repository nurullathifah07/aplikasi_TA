<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\KomponenDarah;
use App\Models\RumahSakit;
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

        // Buat rumah sakit default
        RumahSakit::firstOrCreate(
            ['nama' => 'RS H.Boejasin'],
            ['alamat' => 'Sarang Halang, Pelaihari']
        );

        RumahSakit::firstOrCreate(
            ['nama' => 'RS BCM'],
            ['alamat' => 'Angsau, Pelaihari']
        );

        RumahSakit::firstOrCreate(
            ['nama' => 'Ammariz'],
            ['alamat' => 'Pelaihari']
        );

        RumahSakit::firstOrCreate(
            ['nama' => 'Ibunda'],
            ['alamat' => 'Pelaihari']
        );

        RumahSakit::firstOrCreate(
            ['nama' => 'RS KH Mansyur'],
            ['alamat' => 'Kintap']
        );
    }
}
