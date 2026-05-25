<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\DataLatihController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DataUjiController;
use App\Http\Controllers\PreprocessingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::resource('akun', AkunController::class);
Route::resource('dataset', DatasetController::class);
Route::resource('preprocessing', PreprocessingController::class);

Route::resource('data_latih', DataLatihController::class);
Route::resource('data_uji', DataUjiController::class);
