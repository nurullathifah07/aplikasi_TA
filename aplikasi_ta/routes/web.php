<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (tanpa login)
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (perlu login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Master Data
    Route::resource('akun', AkunController::class);
    Route::resource('rumah-sakit', \App\Http\Controllers\RumahSakitController::class);
    Route::resource('komponen-darah', \App\Http\Controllers\KomponenDarahController::class);

    // Permintaan Darah
    Route::resource('permintaan-darah', \App\Http\Controllers\PermintaanDarahController::class);
    Route::get('permintaan-darah-data', [\App\Http\Controllers\PermintaanDarahController::class, 'data'])->name('permintaan-darah.data');
    Route::post('permintaan-darah/import', [\App\Http\Controllers\PermintaanDarahController::class, 'import'])->name('permintaan-darah.import');
    Route::get('permintaan-darah-template', [\App\Http\Controllers\PermintaanDarahController::class, 'downloadTemplate'])->name('permintaan-darah.template');

    // Stok Darah
    Route::resource('stok-darah', \App\Http\Controllers\StokDarahController::class);

    // Preprocessing
    Route::get('/preprocessing', [\App\Http\Controllers\PreprocessingController::class, 'index'])->name('preprocessing.index');
    Route::get('/preprocessing-data', [\App\Http\Controllers\PreprocessingController::class, 'data'])->name('preprocessing.data');
    Route::post('/preprocessing/proses', [\App\Http\Controllers\PreprocessingController::class, 'proses'])->name('preprocessing.proses');
    Route::get('/preprocessing/cek-data', [\App\Http\Controllers\PreprocessingController::class, 'cekData'])->name('preprocessing.cek-data');

    // Holt's Linear
    Route::get('/holts-linear', [\App\Http\Controllers\HoltsLinearController::class, 'index'])->name('holts.index');
    Route::post('/holts-linear/proses', [\App\Http\Controllers\HoltsLinearController::class, 'proses'])->name('holts.proses');

    // Evaluasi
    Route::get('/evaluasi', [\App\Http\Controllers\EvaluasiController::class, 'index'])->name('evaluasi.index');

    // Prediksi
    Route::get('/prediksi', [\App\Http\Controllers\PrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/generate', [\App\Http\Controllers\PrediksiController::class, 'generate'])->name('prediksi.generate');
});

/*
|--------------------------------------------------------------------------
| Public API Routes (halaman publik tanpa login)
|--------------------------------------------------------------------------
*/

Route::prefix('publik')->group(function () {
    Route::get('/prediksi', [\App\Http\Controllers\PublikController::class, 'prediksi'])->name('publik.prediksi');
    Route::get('/stok-darah', [\App\Http\Controllers\PublikController::class, 'stokDarah'])->name('publik.stok');
    Route::get('/histori-tren', [\App\Http\Controllers\PublikController::class, 'historiTren'])->name('publik.histori');
    Route::get('/histori-tren-data', [\App\Http\Controllers\PublikController::class, 'historiTrenData'])->name('publik.histori.data');
    Route::get('/histori-tren-chart', [\App\Http\Controllers\PublikController::class, 'historiTrenChart'])->name('publik.histori.chart');
});
