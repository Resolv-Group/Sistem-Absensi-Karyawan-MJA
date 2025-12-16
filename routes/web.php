<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BidangUsahaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraKerjaController;
use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('login');
// });

// LOGIN
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'viewDashboardMain'])->name('view.dashboard')->middleware('auth');

//PROFIL
Route::get('/profil/{id}', function () { return view('profil'); })->name('view.profil');
Route::PUT('/profil/update', [ProfilController::class, 'update'])
    ->name('profil.update')
    ->middleware('auth');

//AUTH
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

// Route::middleware('auth')->group(function(){
//     Route::get('/daftar-pekerja', [PekerjaController::class, 'viewPekerjaMain'])->name('view.pekerja');
//     Route::get('/pekerja/tambah', [PekerjaController::class, 'viewTambahPekerja'])->name('view.tambah.pekerja');
//     Route::get('/pekerja/detail/{id}', [PekerjaController::class, 'viewDetailPekerja'])->name('view.detail.pekerja');
//     Route::POST('/tambah-pekerja', [PekerjaController::class, 'tambahPekerja'])->name('tambah.pekerja.post');
//     Route::get('/pekerja/ubah/{id}', [PekerjaController::class, 'ubahPekerja'])->name('view.ubah.pekerja');
//     Route::put('/pekerja/ubah/{id}', [PekerjaController::class, 'updatePekerja'])->name('update.pekerja');
// });

// Route::middleware('auth')->group(function(){
//     Route::get('/daftar-staff', [StaffController::class, 'viewStaffMain'])->name('view.staff');
//     Route::get('/staff/tambah', [StaffController::class, 'viewTambahStaff'])->name('view.tambah.staff');
//     Route::get('/staff/detail', [StaffController::class, 'viewDetailStaff'])->name('view.detail.staff');
//     Route::POST('/tambah-staff', [StaffController::class, 'tambahStaff'])->name('tambah.staff.post');
// });

//MitraKerja


Route::middleware(['auth', 'role:hrd,akuntan,admin'])->group(function(){

    Route::get('/daftar-staff', [StaffController::class, 'viewStaffMain'])->name('view.staff');
    Route::get('/staff/tambah', [StaffController::class, 'viewTambahStaff'])->name('view.tambah.staff');
    Route::post('/tambah-staff', [StaffController::class, 'tambahStaff'])->name('tambah.staff.post');

});

Route::middleware(['auth', 'role:hrd,admin'])->group(function(){

    Route::get('/staff/detail/{id}', [StaffController::class, 'viewDetailStaff'])->name('view.detail.staff');
    Route::get('/staff/ubah/{id}', [StaffController::class, 'ubahStaff'])->name('view.ubah.staff');
    Route::put('/staff/ubah/{id}', [StaffController::class, 'updateStaff'])->name('update.staff');

});

Route::middleware(['auth', 'role:hrd,pic,admin'])->group(function(){

    Route::get('/daftar-pekerja', [PekerjaController::class, 'viewPekerjaMain'])->name('view.pekerja');
    Route::get('/pekerja/tambah', [PekerjaController::class, 'viewTambahPekerja'])->name('view.tambah.pekerja');
    Route::get('/pekerja/detail/{id}', [PekerjaController::class, 'viewDetailPekerja'])->name('view.detail.pekerja');
    Route::POST('/tambah-pekerja', [PekerjaController::class, 'tambahPekerja'])->name('tambah.pekerja.post');
    Route::get('/pekerja/ubah/{id}', [PekerjaController::class, 'ubahPekerja'])->name('view.ubah.pekerja');
    Route::put('/pekerja/ubah/{id}', [PekerjaController::class, 'updatePekerja'])->name('update.pekerja');

    Route::get('/mitra-kerja', [MitraKerjaController::class, 'viewMitraKerjaMain'])->name('view.mitra-kerja');
    Route::get('/mitra-kerja/tambah', [MitraKerjaController::class, 'viewTambahMitraKerja'])->name('view.tambah.mitra-kerja');
    Route::get('/mitra-kerja/detail', [MitraKerjaController::class, 'viewDetailMitraKerja'])->name('view.detail.mitra-kerja');
    Route::POST('/tambah-mitra-kerja', [MitraKerjaController::class, 'tambahMitraKerja'])->name('tambah.mitra-kerja.post');
    Route::get('/mitra-kerja/ubah/{id}', [MitraKerjaController::class, 'ubahMitraKerja'])->name('view.ubah.mitra-kerja');
    Route::put('/mitra-kerja/ubah/{id}', [MitraKerjaController::class, 'updateMitraKerja'])->name('update.mitra-kerja');

    Route::POST('/tambah-bidang-usaha', [BidangUsahaController::class, 'tambahBidangUsaha'])->name('tambah.bidang-usaha.post');
});





