<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', [DashboardController::class, 'viewDashboardMain'])->name('view.dashboard');

Route::middleware('web')->group(function(){
    Route::get('/daftar-pekerja', [PekerjaController::class, 'viewPekerjaMain'])->name('view.pekerja');
    Route::get('/pekerja/tambah', [PekerjaController::class, 'viewTambahPekerja'])->name('view.tambah.pekerja');
    Route::get('/pekerja/detail/{id}', [PekerjaController::class, 'viewDetailPekerja'])->name('view.detail.pekerja');
    Route::POST('/tambah-pekerja', [PekerjaController::class, 'tambahPekerja'])->name('tambah.pekerja.post');
});

Route::middleware('web')->group(function(){
    Route::get('/daftar-staff', [StaffController::class, 'viewStaffMain'])->name('view.staff');
    Route::get('/staff/tambah', [StaffController::class, 'viewTambahStaff'])->name('view.tambah.staff');
    Route::get('/staff/detail', [StaffController::class, 'viewDetailStaff'])->name('view.detail.staff');
});

