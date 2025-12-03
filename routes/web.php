<?php

use App\Http\Controllers\PekerjaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::middleware('web')->group(function(){
    Route::get('/daftar-pekerja', [PekerjaController::class, 'viewPekerjaMain'])->name('view.pekerja');
    Route::get('/t/pekerja', [PekerjaController::class, 'viewTambahPekerja'])->name('view.tambah.pekerja');

});

