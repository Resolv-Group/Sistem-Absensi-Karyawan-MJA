<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BidangUsahaController;
use App\Http\Controllers\BoronganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MitraKerjaController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PKWTController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ShiftAbsenController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

// LOGIN
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'viewDashboardMain'])->name('view.dashboard')->middleware('auth');
Route::post('/penilaian/verify-hrd/{id}', [DashboardController::class, 'verifyPenilaianHrd'])
    ->name('penilaian.verify.hrd')
    ->middleware('auth');

// PROFIL
Route::get('/profil/{id}', function () {
    return view('profil');
})->name('view.profil');
Route::PUT('/profil/update', [ProfilController::class, 'update'])
    ->name('profil.update')
    ->middleware('auth');

// AUTH
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

// MitraKerja

Route::middleware(['auth', 'role:hrd,akuntan,admin'])->group(function () {

    Route::get('/daftar-staff', [StaffController::class, 'viewStaffMain'])->name('view.staff');
    Route::get('/staff/tambah', [StaffController::class, 'viewTambahStaff'])->name('view.tambah.staff');
    Route::post('/tambah-staff', [StaffController::class, 'tambahStaff'])->name('tambah.staff.post');
});

Route::middleware(['auth', 'role:hrd,admin,head_supervisor'])->group(function () {

    Route::get('/staff/detail/{id}', [StaffController::class, 'viewDetailStaff'])->name('view.detail.staff');
    Route::get('/staff/ubah/{id}', [StaffController::class, 'ubahStaff'])->name('view.ubah.staff');
    Route::put('/staff/ubah/{id}', [StaffController::class, 'updateStaff'])->name('update.staff');
    Route::put('/staff/toggle-status/{id}', [StaffController::class, 'toggleStatus']);

    // Mitra Kerja
    Route::get('/mitra-kerja', [MitraKerjaController::class, 'viewMitraKerjaMain'])->name('view.mitra-kerja');
    Route::get('/mitra-kerja/tambah', [MitraKerjaController::class, 'viewTambahMitraKerja'])->name('view.tambah.mitra-kerja');
    Route::get('/mitra-kerja/detail/{id}', [MitraKerjaController::class, 'viewDetailMitraKerja'])->name('view.detail.mitra-kerja');
    Route::POST('/tambah-mitra-kerja', [MitraKerjaController::class, 'tambahMitraKerja'])->name('tambah.mitra-kerja.post');
    Route::get('/mitra-kerja/ubah/{id}', [MitraKerjaController::class, 'ubahMitraKerja'])->name('view.ubah.mitra-kerja');
    Route::put('/mitra-kerja/ubah/{id}', [MitraKerjaController::class, 'updateMitraKerja'])->name('update.mitra-kerja');
    Route::put('/mitra-kerja/toggle-status/{id}', [MitraKerjaController::class, 'toggleStatus']);

    // Tambah Bidang Usaha Mitra Kerja
    Route::POST('/tambah-bidang-usaha', [BidangUsahaController::class, 'tambahBidangUsaha'])->name('tambah.bidang-usaha.post');

    // Unit Spesifik untuk HRD
    Route::get('/unit', [UnitController::class, 'viewUnitMain'])->name('view.unit');
    Route::get('/unit/tambah', [UnitController::class, 'viewTambahUnit'])->name('view.tambah.unit');
    Route::POST('/tambah-unit', [UnitController::class, 'tambahUnit'])->name('tambah.unit.post');
});

Route::middleware(['auth', 'role:hrd,admin,head_supervisor,pic'])->group(function () {
    // Pekerja
    Route::get('/daftar-pekerja', [PekerjaController::class, 'viewPekerjaMain'])->name('view.pekerja');
    Route::get('/pekerja/tambah', [PekerjaController::class, 'viewTambahPekerja'])->name('view.tambah.pekerja');
    Route::get('/pekerja/detail/{id}', [PekerjaController::class, 'viewDetailPekerja'])->name('view.detail.pekerja');
    Route::POST('/tambah-pekerja', [PekerjaController::class, 'tambahPekerja'])->name('tambah.pekerja.post');
    Route::get('/pekerja/ubah/{id}', [PekerjaController::class, 'ubahPekerja'])->name('view.ubah.pekerja');
    Route::put('/pekerja/ubah/{id}', [PekerjaController::class, 'updatePekerja'])->name('update.pekerja');
    // Route::get('/pekerja/dokumen/{id}', [PekerjaController::class, 'showDokumen'])->name('pekerja.dokumen.show');
    Route::get('/pkwt/dokumen/{id}', [PekerjaController::class, 'showPkwtDokumen'])->name('pkwt.dokumen.show'); // Route for specific PKWT records (Current and History)
    Route::put('/pekerja/toggle-status/{id}', [PekerjaController::class, 'toggleStatus']);

    Route::post('/pekerja/histori-pkwt/tambah', [PekerjaController::class, 'TambahHistoriPKWT'])->name('pekerja.histori-pkwt.tambah');
});

Route::middleware(['auth', 'role:hrd,admin,akuntan,pic'])->group(function () {
    // Payroll
    Route::get('/payroll', [PayrollController::class, 'viewPayrollMain'])->name('view.payroll');
    Route::get('/payroll/overview', [PayrollController::class, 'viewPayrollOverview'])->name('view.payroll.overview');
    Route::post('/payroll/overview', [PayrollController::class, 'overviewPayroll'])->name('overview.payroll');
    Route::post('/export-detail-harian', [PayrollController::class, 'ExportDetailHarian'])->name('export.detail.harian');
    Route::get('/export-detail-borongan', [PayrollController::class, 'ExportDetailBorongan'])->name('export.detail.borongan');
    Route::get('/export-tanda-terima-borongan', [PayrollController::class, 'ExportTandaTerimaBorongan'])->name('export.tanda-terima.borongan');
    Route::get('/export-invoice-borongan', [PayrollController::class, 'ExportInvoiceBorongan'])->name('export.invoice.borongan');
    Route::get('/export-kwitansi-borongan', [PayrollController::class, 'ExportKwitansiBorongan'])->name('export.kwitansi.borongan');
    Route::post('/export-rincian-upah-borongan', [PayrollController::class, 'ExportRincianUpahBorongan'])->name('export.rincian.upah.borongan');
    Route::post('/export-rincian-upah-harian', [PayrollController::class, 'ExportRincianUpahHarian'])->name('export.rincian.upah.harian');
    Route::post('/export-rincian-upah-harian-pekerja', [PayrollController::class, 'ExportRincianUpahHarianPerPekerja'])->name('export.rincian.upah.harian.pekerja');
    Route::post('/payroll/dispatch-emails', [PayrollController::class, 'dispatchPayrollEmails'])->name('payroll.dispatch.emails');

    Route::post('/export-daily-report-harian', [PayrollController::class, 'ExportDailyReportHarian'])->name('export.daily.report.harian');

    Route::post('/export-summary-upah-harian', [PayrollController::class, 'SummaryUpahHarian'])->name('export.summary.upah.harian');

    Route::post('/payroll/get-adjustments', [AbsensiController::class, 'getAdjustments'])->name('payroll.get-adjustments');

    Route::post('/export-borongan-kelompok', [PayrollController::class, 'ExportBoronganKelompok'])->name('export.borongan.kelompok');
});

Route::middleware(['auth', 'role:pic,admin'])->group(function () {
    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'viewAbsensiMain'])->name('view.absensi');
    Route::get('/absensi/{id_unit}/harian/{date}', [AbsensiController::class, 'ViewHarian'])->name('view.absensi.harian');
    Route::get('/absensi/{id_unit}/borongan/{date}', [AbsensiController::class, 'ViewBorongan'])->name('view.absensi.borongan');
    Route::put('/absensi/{id_unit}/harian/{date}/bulk-update-harian', [AbsensiController::class, 'bulkAbsensiUpdate'])->name('absensi.bulk.update');
    Route::put('/absensi/{id_unit}/harian/{date}/bulk-update-status-harian', [AbsensiController::class, 'bulkAbsensiUpdateStatus'])->name('absensi.bulk.update-status');
    Route::put('/absensi/{id_unit}/borongan/{date}/bulk-update-borongan', [AbsensiController::class, 'bulkAbsensiBoronganUpdate'])->name('absensi.borongan.bulk.update');
    Route::put('/absensi/{id_unit}/borongan/{date}/bulk-update-kelompok-borongan', [AbsensiController::class, 'bulkKelompokAbsensiBoronganUpdate'])->name('absensi.kelompok.borongan.bulk.update');

    // Absensi -> Tunjangan
    Route::post('/absensi/{id_unit}/harian/{date}/bulk-update-tunjangan', [AbsensiController::class, 'bulkAbsensiUpdateTunjangan'])->name('absensi.bulk.store-tunjangan');
    Route::post('/absensi/{id_unit}/harian/{date}/bulk-update-potongan', [AbsensiController::class, 'bulkAbsensiUpdatePotongan'])->name('absensi.bulk.store-potongan');

    // Penilaian
    // Filtered
    Route::get('/penilaian/unit/{id}', [PenilaianController::class, 'viewPenilaianMain'])->name('view.penilaian');
    // =--=
    Route::get('/penilaian/unit/{unitId}/buat-penilaian', [PenilaianController::class, 'viewBuatPenilaian'])->name('view.buat.penilaian');
    Route::post('/buat-penilaian', [PenilaianController::class, 'buatPenilaian'])->name('buat.penilaian');
    Route::get('/penilaian/{penilaianId}/unit/{unitId}/pekerja/{pekerjaId}/', [PenilaianController::class, 'viewUbahPenilaian'])->name('view.ubah.penilaian');
    Route::put('/penilaian/{penilaianId}/unit/{unitId}/pekerja/{pekerjaId}/ubah', [PenilaianController::class, 'ubahPenilaian'])->name('ubah.penilaian');
    Route::put('/penilaian/unit/bulk-update', [PenilaianController::class, 'bulkUpdateStatus'])->name('bulk.update.penilaian.pekerja');
    Route::post('/penilaian/unit/{unitId}', [PenilaianController::class, 'ExportExcel'])->name('export.excel');
});

Route::middleware(['auth', 'role:hrd,pic,admin,head_supervisor'])->group(function () {
    // Import Pekerja-Borongan
    Route::post('/pekerja/import', [PekerjaController::class, 'importExcel'])->name('pekerja.import');
    Route::post('/borongan/import', [BoronganController::class, 'importExcel'])->name('borongan.import');
    // Unit -> Main - Detail
    // Filtered
    Route::get('/unit/detail/{id}', [UnitController::class, 'viewDetailUnit'])->name('view.detail.unit');
    Route::get('/unit/ubah/{id}', [UnitController::class, 'ubahUnit'])->name('view.ubah.unit');
    // =--=
    Route::put('/unit/ubah/{id}', [UnitController::class, 'updateUnit'])->name('update.unit');
    Route::put('/unit/detail/{id}/shifts', [ShiftAbsenController::class, 'update'])->name('unit.shifts.update');

    // Unit -> Asset
    Route::post('/unit/detail/{id}/asset', [UnitController::class, 'storeBulkAsset'])->name('tambah.asset.post');
    Route::put('/unit/detail/{id}/asset', [UnitController::class, 'updateBulkAsset'])->name('update.asset');
    Route::delete('/unit/{id_unit}/asset/destroy', [UnitController::class, 'destroyAsset'])->name('asset.destroy');
    Route::post('/unit/detail/{id}/asset/export', [UnitController::class, 'exportAsset'])->name('export.asset');

    // Unit -> Kas Kecil
    Route::post('/unit/detail/{id}/kas-kecil', [UnitController::class, 'storeBulkKas'])->name('tambah.kas-kecil.post');
    Route::put('/unit/detail/{id}/kas-kecil', [UnitController::class, 'updateBulkKas'])->name('update.kas-kecil');
    Route::delete('/unit/{id_unit}/kas-kecil/destroy', [UnitController::class, 'destroyKasKecil'])->name('kas-kecil.destroy');
    Route::get('/unit/{id}/kas-kecil/nota', [UnitController::class, 'showKasNota'])->name('kas-kecil.nota');
    Route::post('/unit/detail/{id}/kas-kecil/export', [UnitController::class, 'exportKasKecil'])->name('export.kas-kecil');

    // Unit -> Status
    Route::put('/unit/toggle-status/{id}', [UnitController::class, 'toggleStatus']);

    // Unit -> PKWT
    // Filtered
    Route::get('/unit/{id}/daftar-pkwt', [PKWTController::class, 'viewPKWTMain'])->name('view.pkwt');
    Route::get('/unit/{id}/tambah-pekerja', [PKWTController::class, 'viewTambahUnitHarian'])->name('view.tambah.unit-pekerja');
    // =--=
    Route::POST('/tambah-unit-pekerja', [PKWTController::class, 'tambahPekerjaUnit'])->name('tambah.unit-pekerja.post');
    Route::get('/unit/{unitId}/pekerja/{pekerjaId}/ubah', [PKWTController::class, 'ubahUnitPekerja'])->name('view.ubah.unit-pekerja');
    Route::put('/unit/{unitId}/pekerja/{pekerjaId}/ubah', [PKWTController::class, 'updateUnitPekerja'])->name('update.unit-pekerja');
    Route::put('/unit/pekerja/bulk-update-status', [PKWTController::class, 'bulkUpdateStatus'])->name('bulk.update.pekerja');
    Route::put('/unit/pekerja/bulk-update-divisi', [PKWTController::class, 'bulkUpdateDivisi'])->name('bulk.update.divisi');
    Route::put('/unit/pekerja/bulk-update-jabatan', [PKWTController::class, 'bulkUpdateJabatan'])->name('bulk.update.jabatan');

    Route::post('/quick-store/{type}', [PKWTController::class, 'quickStore']);

    // Route::put('/unit/{unitId}/pekerja/{pekerjaId]/toggle-status/', [UnitController::class, 'toggleStatusPKWT']);

    // Unit -> Borongan
    // =--=
    Route::get('/unit/{id}/daftar-borongan', [BoronganController::class, 'viewBoronganMain'])->name('view.borongan');
    Route::get('/unit/{id}/tambah-borongan', [BoronganController::class, 'viewTambahBorongan'])->name('view.tambah.unit-borongan');
    // =--=
    Route::POST('/tambah-unit-borongan', [BoronganController::class, 'tambahBoronganUnit'])->name('tambah.unit-borongan.post');
    Route::get('/unit/{unitId}/borongan/{boronganId}/ubah', [BoronganController::class, 'ubahUnitBorongan'])->name('view.ubah.unit-borongan');
    Route::put('/unit/{unitId}/borongan/{boronganId}/ubah', [BoronganController::class, 'updateUnitBorongan'])->name('update.unit-borongan');
    Route::put('/unit/borongan/bulk-update', [BoronganController::class, 'bulkUpdateBorongan'])->name('bulk.update.borongan');
    Route::put('/unit/borongan/bulk-update-kategori', [BoronganController::class, 'bulkUpdateKategori'])->name('bulk.update.kategori');
    Route::put('/unit/borongan/bulk-update-status', [BoronganController::class, 'bulkUpdateStatus'])->name('bulk.update.borongan');

    // Show Dokumen
    Route::get('/unit/{id}/stream/mou', [UnitController::class, 'showDokumenMOU'])
        ->name('stream.mou');
    Route::get('/pkwt/{id}/stream/pkwt', [UnitController::class, 'showDokumenPKWT'])
        ->name('stream.pkwt');

    // Tambah Kategori Unit - Borongan
    Route::POST('/tambah-kategori', [KategoriController::class, 'tambahKategori'])->name('tambah.kategori.post');
    // Tambah Satuan Unit - Borongan
    Route::POST('/tambah-satuan', [SatuanController::class, 'tambahSatuan'])->name('tambah.satuan.post');

});
