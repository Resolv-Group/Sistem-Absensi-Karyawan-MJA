<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Detil_Borongan;
use App\Models\Detil_Harian;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\Penilaian_Pkwt;
use App\Models\PicUnit;
use App\Models\PKWT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    /**
     * Terjemahkan error code MySQL ke pesan ramah pengguna.
     */
    private function translateDbError(QueryException $e): string
    {
        $errorCode = $e->errorInfo[1] ?? null;

        return match ($errorCode) {
            1062 => 'Data yang Anda masukkan sudah ada di sistem. Mohon periksa kembali data yang bersifat unik(tidak boleh sama).',
            1452 => 'Data referensi tidak valid. Pastikan data terkait masih terdaftar di sistem.',
            1048 => 'Terdapat kolom wajib yang belum diisi. Mohon lengkapi seluruh data yang diperlukan.',
            1406 => 'Data yang dimasukkan terlalu panjang. Mohon persingkat input Anda.',
            1264 => 'Nilai angka yang dimasukkan di luar batas yang diizinkan. Mohon periksa kembali.',
            default => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
        };
    }
    // function viewDashboardMain()
    // {
    //     $totalPekerja = Pekerja::count();
    //     $totalMitra = MitraKerja::count();
    //     $pegawaiBulanIni = Pekerja::whereMonth('created_at', Carbon::now()->month)
    //         ->whereYear('created_at', Carbon::now()->year)
    //         ->count();

    //     $year = request('year', now()->year);

    //     // jumlah pegawai baru per bulan (NON kumulatif)
    //     $pegawaiPerBulan = Pekerja::select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))->whereYear('created_at', $year)->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');

    //     // pastikan Jan–Des selalu ada (default 0)
    //     $monthlyData = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $monthlyData[] = $pegawaiPerBulan[$i] ?? 0;
    //     }

    //     $mitraMendekati = MitraKerja::where('status_aktif', 1)
    //         ->whereNotNull('tgl_akhir_mou')
    //         ->whereBetween('tgl_akhir_mou', [now(), now()->addDays(30)])
    //         ->count();

    //     // --- TAMBAHKAN LOGIKA BARU DI SINI ---
    //     $today = Carbon::today();

    //     // 1. Hadir Hari Ini (Status 1)
    //     $hadirHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
    //         $q->whereDate('tgl_absensi', $today);
    //     })
    //         ->where('status_kehadiran', 1)
    //         ->count();

    //     // 2. Izin / Sakit Hari Ini (Status 2)
    //     $izinSakitHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
    //         $q->whereDate('tgl_absensi', $today);
    //     })
    //         ->where('status_kehadiran', 2)
    //         ->count();

    //     // 3. Overtime Hari Ini
    //     $overtimeHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
    //         $q->whereDate('tgl_absensi', $today);
    //     })
    //     ->where('overtime', '>', 0)
    //     ->count();

    //     // Count total attendance for the header badge
    //     $totalAbsensiHarian = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
    //         $q->whereDate('tgl_absensi', $today);
    //     })->count();

    //     $kehadiranTerbaru = Detil_Harian::with(['absensi.pekerja', 'absensi.unit'])
    //         ->whereHas('absensi', function ($q) use ($today) {
    //             $q->whereDate('tgl_absensi', $today);
    //         })
    //         // Including statuses: 1 (Hadir), 2 (Izin), 3 (Cuti), 4 (Sakit), 5 (Rencana Cuti), 6 (Absen)
    //         ->whereIn('status_kehadiran', [1, 2, 3, 4, 5, 6])
    //         ->orderBy('updated_at', 'desc')
    //         ->limit(5)
    //         ->get();

    //     // Count total attendance for the header badge
    //     $totalAbsensiBorongan = Detil_Borongan::whereHas('absensi', function ($q) use ($today) {
    //             $q->whereDate('tgl_absensi', $today);
    //         })
    //         ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
    //         ->distinct('absensi.id_pekerja')
    //         ->count('absensi.id_pekerja');

    //     $boronganTerbaru = Detil_Borongan::query()
    //         ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
    //         ->join('pekerja', 'absensi.id_pekerja', '=', 'pekerja.id') // Ganti 'pekerjas' jika nama tabel pekerja berbeda
    //         ->join('unit', 'absensi.id_unit', '=', 'unit.id_unit')    // Ganti 'units' jika nama tabel unit berbeda
    //         ->whereDate('absensi.tgl_absensi', $today)
    //         ->select(
    //             'pekerja.nama as nama_pekerja',
    //             'pekerja.nik as nik_pekerja',
    //             'unit.nama_unit as nama_unit',
    //             \DB::raw('SUM(detil_borongan.FD + detil_borongan.good_mc + detil_borongan.act_rej) as total_sum_qty'),
    //             \DB::raw('MAX(detil_borongan.status_kehadiran) as status_kehadiran'),
    //             \DB::raw('MAX(detil_borongan.updated_at) as last_entry_at')
    //         )
    //         ->groupBy('pekerja.id', 'pekerja.nama', 'pekerja.nik', 'unit.id_unit', 'unit.nama_unit')
    //         ->orderBy('last_entry_at', 'desc')
    //         ->limit(5)
    //         ->get();
    //     // dd($boronganTerbaru);

    //     $today = now()->startOfDay();
    //     $thirtyDaysLater = now()->addDays(30)->endOfDay();

    //     $urgentKontrak = PKWT::with('pekerja', 'unit')
    //         ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
    //         ->orderBy('tgl_akhir_pkwt', 'asc')
    //         ->first();

    //     // --- 1. PKWT EXPIRED (Sudah lewat tanggal tapi status_aktif masih 1) ---
    //     $expiredKontrakList = PKWT::with(['pekerja', 'unit'])
    //         ->whereHas('pekerja', function ($q) {
    //             $q->where('status_aktif', 1);
    //         })
    //         ->where('tgl_akhir_pkwt', '<', $today)
    //         ->orderBy('tgl_akhir_pkwt', 'asc')
    //         ->get();

    //     $urgentExpiredKontrak = $expiredKontrakList->first();
    //     $totalExpiredKontrak = $expiredKontrakList->count();
    //     $othersExpiredKontrak = $expiredKontrakList->skip(1);
    //     $lewatHariKontrak = $urgentExpiredKontrak ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredKontrak->tgl_akhir_pkwt), false)) : 0;

    //     // --- 2. PKWT AKAN BERAKHIR (Logika existing Anda) ---
    //     $kontrakMendekatiList = PKWT::with(['pekerja', 'unit'])
    //         ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
    //         ->orderBy('tgl_akhir_pkwt', 'asc')
    //         ->get();

    //     $urgentKontrak = $kontrakMendekatiList->first();
    //     $totalKontrakMendekati = $kontrakMendekatiList->count();
    //     $othersKontrak = $kontrakMendekatiList->skip(1);
    //     // Hitung sisa hari untuk yang urgent
    //     $sisaHari = $urgentKontrak ? Carbon::today()->diffInDays(Carbon::parse($urgentKontrak->tgl_akhir_pkwt), false) : 0;

    //     // 1. MITRA EXPIRED (Sudah lewat tapi masih aktif)
    //     $mitraExpiredList = MitraKerja::where('status_aktif', 1)->whereNotNull('tgl_akhir_mou')->where('tgl_akhir_mou', '<', $today)->orderBy('tgl_akhir_mou', 'asc')->get();

    //     $urgentExpiredMitra = $mitraExpiredList->first();
    //     $totalExpiredMitra = $mitraExpiredList->count();
    //     $othersExpiredMitra = $mitraExpiredList->skip(1);

    //     // 2. MITRA MENDEKATI HABIS (Dalam 30 hari ke depan)
    //     $mitraMendekatiList = MitraKerja::where('status_aktif', 1)
    //         ->whereNotNull('tgl_akhir_mou')
    //         ->whereBetween('tgl_akhir_mou', [$today, $thirtyDaysLater])
    //         ->orderBy('tgl_akhir_mou', 'asc')
    //         ->get();

    //     $urgentMitra = $mitraMendekatiList->first();
    //     $totalMitraMendekati = $mitraMendekatiList->count();
    //     $othersMitra = $mitraMendekatiList->skip(1);

    //     // --- HITUNG SISA HARI (Aman untuk UI) ---
    //     $sisaHariMitra = $urgentMitra ? Carbon::today()->diffInDays(Carbon::parse($urgentMitra->tgl_akhir_mou), false) : 0;

    //     $lewatHariMitra = $urgentExpiredMitra ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredMitra->tgl_akhir_mou), false)) : 0;

    //     // 2. Hitung total kontrak yang memenuhi kriteria (untuk angka +X lainnya)
    //     $totalKontrakMendekati = PKWT::whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])->count();

    //     // Ambil semua data kontrak yang mendekati (Misal limit 5 untuk performa)
    //     $kontrakMendekatiList = PKWT::with('pekerja')
    //         ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
    //         ->orderBy('tgl_akhir_pkwt', 'asc')
    //         ->get();

    //     $urgentKontrak = $kontrakMendekatiList->first();
    //     $totalKontrakMendekati = $kontrakMendekatiList->count();
    //     // Sisanya (untuk list dropdown)
    //     $othersKontrak = $kontrakMendekatiList->skip(1);

    //     $absensiPendingCount = Absensi::where('verifikasi', 0)->count();

    //     $penilaianTerbaru = Penilaian_Pkwt::with(['pekerja', 'unit']) // Eager load relasi
    //         ->orderBy('created_at', 'desc')
    //         ->limit(5)
    //         ->get();

    //     $userRole = auth()->user()->role;
    //     $penilaianQuery = Penilaian_Pkwt::with(['pekerja', 'unit']);
        
    //     if ($userRole === 'pic') {
    //         $penilaianQuery->where('status_pic', 0);
    //     } elseif ($userRole === 'hrd') {
    //         $penilaianQuery->where('status_hrd', 0);
    //     } else {
    //         $penilaianQuery->where(function ($q) {
    //             $q->where('status_pic', 0)->orWhere('status_hrd', 0);
    //         });
    //     }
        
    //     $penilaianPending = $penilaianQuery->orderBy('created_at', 'desc')->get();

    //     return view(
    //         'dashboard',
    //         [
    //             'employeeChartData' => $monthlyData,
    //             'selectedYear' => $year,
    //             'totalAbsensiHarian' => $totalAbsensiHarian,
    //             'kehadiranTerbaru' => $kehadiranTerbaru,
    //             'totalAbsensiBorongan' => $totalAbsensiBorongan,
    //             'boronganTerbaru' => $boronganTerbaru,
    //             'penilaianTerbaru' => $penilaianTerbaru,
    //             'penilaianPending' => $penilaianPending,
    //             'urgentKontrak' => $urgentKontrak,
    //             'urgentExpiredKontrak' => $urgentExpiredKontrak,
    //             'totalExpiredKontrak' => $totalExpiredKontrak,
    //             'othersExpiredKontrak' => $othersExpiredKontrak,
    //             'lewatHariKontrak' => $lewatHariKontrak,
    //             'sisaHari' => $sisaHari,
    //             'urgentMitra' => $urgentMitra,
    //             'totalMitraMendekati' => $totalMitraMendekati,
    //             'sisaHariMitra' => $sisaHariMitra,
    //             'othersMitra' => $othersMitra,
    //             'urgentExpiredMitra' => $urgentExpiredMitra,
    //             'totalExpiredMitra' => $totalExpiredMitra,
    //             'othersExpiredMitra' => $othersExpiredMitra,
    //             'lewatHariMitra' => $lewatHariMitra,
    //             'totalKontrakMendekati' => $totalKontrakMendekati,
    //             'absensiPendingCount' => $absensiPendingCount,
    //             'othersKontrak' => $othersKontrak,
    //             'overtimeHariIni' => $overtimeHariIni,
    //         ],
    //         compact(
    //             'totalPekerja',
    //             'totalMitra',
    //             'pegawaiBulanIni',
    //             'mitraMendekati',
    //             'hadirHariIni', // Pass ke View
    //             'izinSakitHariIni', // Pass ke View
    //         ),
    //     );
    // }

    function viewDashboardMain()
{
    $user = auth()->user();
    $userRole = $user->role;
    $staffId = $user->staff_id;
    $isPic = $userRole === 'pic';

    // Get accessible unit IDs for PIC users only
    $accessibleUnitIds = $isPic
        ? PicUnit::where('id_pic', $staffId)->pluck('id_unit')->toArray()
        : [];

    $totalPekerja = Pekerja::count();
    $totalMitra = MitraKerja::count();
    $pegawaiBulanIni = Pekerja::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

    $year = request('year', now()->year);

    $pegawaiPerBulan = Pekerja::select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $year)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->pluck('total', 'bulan');

    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthlyData[] = $pegawaiPerBulan[$i] ?? 0;
    }

    $mitraMendekati = MitraKerja::where('status_aktif', 1)
        ->whereNotNull('tgl_akhir_mou')
        ->whereBetween('tgl_akhir_mou', [now(), now()->addDays(30)])
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereHas('units', function ($qq) use ($accessibleUnitIds) {
                $qq->whereIn('id', $accessibleUnitIds);
            });
        })
        ->count();

    $today = Carbon::today();

    // 1. Hadir Hari Ini
    $hadirHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
        $q->whereDate('tgl_absensi', $today);
        if ($isPic) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        }
    })
        ->where('status_kehadiran', 1)
        ->count();

    // 2. Izin / Sakit Hari Ini
    $izinSakitHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
        $q->whereDate('tgl_absensi', $today);
        if ($isPic) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        }
    })
        ->where('status_kehadiran', 2)
        ->count();

    // 3. Overtime Hari Ini
    $overtimeHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
        $q->whereDate('tgl_absensi', $today);
        if ($isPic) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        }
    })
        ->where('overtime', '>', 0)
        ->count();

    $totalAbsensiHarian = Detil_Harian::whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
        $q->whereDate('tgl_absensi', $today);
        if ($isPic) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        }
    })->count();

    $kehadiranTerbaru = Detil_Harian::with(['absensi.pekerja', 'absensi.unit'])
        ->whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
            $q->whereDate('tgl_absensi', $today);
            if ($isPic) {
                $q->whereIn('id_unit', $accessibleUnitIds);
            }
        })
        ->whereIn('status_kehadiran', [1, 2, 3, 4, 5, 6])
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get();

    $totalAbsensiBoronganQuery = Detil_Borongan::whereHas('absensi', function ($q) use ($today, $isPic, $accessibleUnitIds) {
        $q->whereDate('tgl_absensi', $today);
        if ($isPic) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        }
    })
        ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
        ->distinct('absensi.id_pekerja');

    $totalAbsensiBorongan = $totalAbsensiBoronganQuery->count('absensi.id_pekerja');

    $boronganTerbaru = Detil_Borongan::query()
        ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
        ->join('pekerja', 'absensi.id_pekerja', '=', 'pekerja.id')
        ->join('unit', 'absensi.id_unit', '=', 'unit.id_unit')
        ->whereDate('absensi.tgl_absensi', $today)
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('absensi.id_unit', $accessibleUnitIds);
        })
        ->select(
            'pekerja.nama as nama_pekerja',
            'pekerja.nik as nik_pekerja',
            'unit.nama_unit as nama_unit',
            \DB::raw('SUM(detil_borongan.FD + detil_borongan.good_mc + detil_borongan.act_rej) as total_sum_qty'),
            \DB::raw('MAX(detil_borongan.status_kehadiran) as status_kehadiran'),
            \DB::raw('MAX(detil_borongan.updated_at) as last_entry_at')
        )
        ->groupBy('pekerja.id', 'pekerja.nama', 'pekerja.nik', 'unit.id_unit', 'unit.nama_unit')
        ->orderBy('last_entry_at', 'desc')
        ->limit(5)
        ->get();

    $today = now()->startOfDay();
    $thirtyDaysLater = now()->addDays(30)->endOfDay();

    // --- 1. PKWT EXPIRED ---
    $expiredKontrakList = PKWT::with(['pekerja', 'unit'])
        ->whereHas('pekerja', function ($q) {
            $q->where('status_aktif', 1);
        })
        ->where('tgl_akhir_pkwt', '<', $today)
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        })
        ->orderBy('tgl_akhir_pkwt', 'asc')
        ->get();

    

    $urgentExpiredKontrak = $expiredKontrakList->first();
    $totalExpiredKontrak = $expiredKontrakList->count();
    $othersExpiredKontrak = $expiredKontrakList->skip(1);
    $lewatHariKontrak = $urgentExpiredKontrak ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredKontrak->tgl_akhir_pkwt), false)) : 0;

    // --- 2. PKWT AKAN BERAKHIR ---
    $kontrakMendekatiList = PKWT::with(['pekerja', 'unit'])
        ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        })
        ->orderBy('tgl_akhir_pkwt', 'asc')
        ->get();

    $urgentKontrak = $kontrakMendekatiList->first();
    $totalKontrakMendekati = $kontrakMendekatiList->count();
    $othersKontrak = $kontrakMendekatiList->skip(1);
    $sisaHari = $urgentKontrak ? Carbon::today()->diffInDays(Carbon::parse($urgentKontrak->tgl_akhir_pkwt), false) : 0;

    // 1. MITRA EXPIRED & 2. MITRA MENDEKATI HABIS (PIC does not see mitra alerts)
    if ($isPic) {
        $mitraExpiredList = collect();
        $mitraMendekatiList = collect();
    } else {
        $mitraExpiredList = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->where('tgl_akhir_mou', '<', $today)
            ->orderBy('tgl_akhir_mou', 'asc')
            ->get();

        $mitraMendekatiList = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->whereBetween('tgl_akhir_mou', [$today, $thirtyDaysLater])
            ->orderBy('tgl_akhir_mou', 'asc')
            ->get();
    }

    $urgentExpiredMitra = $mitraExpiredList->first();
    $totalExpiredMitra = $mitraExpiredList->count();
    $othersExpiredMitra = $mitraExpiredList->skip(1);

    $urgentMitra = $mitraMendekatiList->first();
    $totalMitraMendekati = $mitraMendekatiList->count();
    $othersMitra = $mitraMendekatiList->skip(1);

    $sisaHariMitra = $urgentMitra ? Carbon::today()->diffInDays(Carbon::parse($urgentMitra->tgl_akhir_mou), false) : 0;
    $lewatHariMitra = $urgentExpiredMitra ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredMitra->tgl_akhir_mou), false)) : 0;

    $absensiPendingCount = Absensi::where('verifikasi', 0)
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        })
        ->count();

    $penilaianTerbaru = Penilaian_Pkwt::with(['pekerja', 'unit'])
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $penilaianQuery = Penilaian_Pkwt::with(['pekerja', 'unit'])
        ->when($isPic, function ($q) use ($accessibleUnitIds) {
            $q->whereIn('id_unit', $accessibleUnitIds);
        });

    if ($userRole === 'pic') {
        $penilaianQuery->where('status_pic', 0);
    } elseif ($userRole === 'hrd') {
        $penilaianQuery->where('status_hrd', 0);
    } else {
        $penilaianQuery->where(function ($q) {
            $q->where('status_pic', 0)->orWhere('status_hrd', 0);
        });
    }

    $penilaianPending = $penilaianQuery->orderBy('created_at', 'desc')->get();

    return view(
        'dashboard',
        [
            'employeeChartData' => $monthlyData,
            'selectedYear' => $year,
            'totalAbsensiHarian' => $totalAbsensiHarian,
            'kehadiranTerbaru' => $kehadiranTerbaru,
            'totalAbsensiBorongan' => $totalAbsensiBorongan,
            'boronganTerbaru' => $boronganTerbaru,
            'penilaianTerbaru' => $penilaianTerbaru,
            'penilaianPending' => $penilaianPending,
            'urgentKontrak' => $urgentKontrak,
            'urgentExpiredKontrak' => $urgentExpiredKontrak,
            'totalExpiredKontrak' => $totalExpiredKontrak,
            'othersExpiredKontrak' => $othersExpiredKontrak,
            'lewatHariKontrak' => $lewatHariKontrak,
            'sisaHari' => $sisaHari,
            'urgentMitra' => $urgentMitra,
            'totalMitraMendekati' => $totalMitraMendekati,
            'sisaHariMitra' => $sisaHariMitra,
            'othersMitra' => $othersMitra,
            'urgentExpiredMitra' => $urgentExpiredMitra,
            'totalExpiredMitra' => $totalExpiredMitra,
            'othersExpiredMitra' => $othersExpiredMitra,
            'lewatHariMitra' => $lewatHariMitra,
            'totalKontrakMendekati' => $totalKontrakMendekati,
            'absensiPendingCount' => $absensiPendingCount,
            'othersKontrak' => $othersKontrak,
            'overtimeHariIni' => $overtimeHariIni,
        ],
        compact(
            'totalPekerja',
            'totalMitra',
            'pegawaiBulanIni',
            'mitraMendekati',
            'hadirHariIni',
            'izinSakitHariIni',
        ),
    );

    //test
}

    public function verifyPenilaianHrd($id)
    {
        try {
            // 1. Cari data penilaian berdasarkan ID
            $penilaian = Penilaian_Pkwt::findOrFail($id);

            $User = User::where('id', auth()->id())->first();

            if ($User->role == 'pic') {
                $penilaian->update([
                    'status_pic' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            } elseif ($User->role == 'hrd') {
                $penilaian->update([
                    'status_hrd' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            } else {
                if ($penilaian->status_pic == 0) {
                    $penilaian->update([
                        'status_pic' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                } elseif ($penilaian->status_hrd == 0) {
                    $penilaian->update([
                        'status_hrd' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            // 2. Update status_hrd dan catat siapa yang mengupdate

            // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
            return back()->with('success', 'Penilaian untuk ' . $penilaian->pekerja->nama . ' berhasil diverifikasi.');
        } catch (QueryException $e) {
            \Log::error('verifyPenilaianHrd DB Error: ' . $e->getMessage());
            return back()->with('error', $this->translateDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('verifyPenilaianHrd General Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
