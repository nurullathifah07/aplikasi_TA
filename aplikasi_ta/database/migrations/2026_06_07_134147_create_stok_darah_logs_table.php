<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_darah_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_darah_id')->constrained('stok_darah')->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->foreignId('permintaan_darah_id')->nullable()->constrained('permintaan_darah')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_darah_log');
    }
};
