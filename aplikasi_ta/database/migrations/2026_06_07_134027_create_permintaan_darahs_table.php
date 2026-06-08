<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_darah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rumah_sakit_id')->constrained('rumah_sakit')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->foreignId('komponen_darah_id')->constrained('komponen_darah')->onDelete('cascade');
            $table->integer('jumlah');
            $table->enum('status', ['pending', 'terpenuhi', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_darah');
    }
};
