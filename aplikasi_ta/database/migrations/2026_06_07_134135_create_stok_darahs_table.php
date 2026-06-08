<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_darah', function (Blueprint $table) {
            $table->id();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->foreignId('komponen_darah_id')->constrained('komponen_darah')->onDelete('cascade');
            $table->integer('jumlah')->default(0);
            $table->timestamps();

            $table->unique(['golongan_darah', 'komponen_darah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_darah');
    }
};
