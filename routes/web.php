<?php

use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\StaffController;
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
    Route::post('/tambah-pekerja', [PekerjaController::class, 'tambahPekerja']);
});

Route::middleware('web')->group(function(){
    Route::get('/daftar-staff', [StaffController::class, 'viewStaffMain'])->name('view.staff');
    Route::get('/t/staff', [StaffController::class, 'viewTambahStaff'])->name('view.tambah.staff');
});

