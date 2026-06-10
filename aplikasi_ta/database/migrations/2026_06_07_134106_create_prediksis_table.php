<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_prediksi'); // tanggal saat prediksi dijalankan
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->foreignId('komponen_darah_id')->constrained('komponen_darah')->onDelete('cascade');
            $table->date('tanggal_target'); // tanggal yang diprediksi
            $table->decimal('nilai_prediksi', 10, 2);
            $table->decimal('alpha', 5, 4); // parameter smoothing level
            $table->decimal('beta', 5, 4); // parameter smoothing trend
            $table->decimal('rmse', 10, 4)->nullable();
            $table->decimal('mape', 10, 4)->nullable();
            $table->decimal('mae', 10, 4)->nullable();
            $table->string('rasio_split'); // misal "80:20"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediksi');
    }
};
