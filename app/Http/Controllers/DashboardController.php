<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Detil_Borongan;
use App\Models\Detil_Harian;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function viewDashboardMain()
    {
        $totalPekerja = Pekerja::count();
        $totalMitra = MitraKerja::count();
        $pegawaiBulanIni = Pekerja::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $year = request('year', now()->year);

        // jumlah pegawai baru per bulan (NON kumulatif)
        $pegawaiPerBulan = Pekerja::select(DB::raw('MONTH(created_at) as bulan'), DB::raw('COUNT(*) as total'))->whereYear('created_at', $year)->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');

        // pastikan Jan–Des selalu ada (default 0)
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $pegawaiPerBulan[$i] ?? 0;
        }

        $mitraMendekati = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->whereBetween('tgl_akhir_mou', [now(), now()->addDays(30)])
            ->count();

        // --- TAMBAHKAN LOGIKA BARU DI SINI ---
        $today = Carbon::today();

        // 1. Hadir Hari Ini (Status 1)
        $hadirHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
            $q->whereDate('tgl_absensi', $today);
        })
            ->where('status_kehadiran', 1)
            ->count();

        // 2. Izin / Sakit Hari Ini (Status 2)
        $izinSakitHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
            $q->whereDate('tgl_absensi', $today);
        })
            ->where('status_kehadiran', 2)
            ->count();

        // 3. Terlambat Hari Ini
        // Menghitung yang hadir (status 1) tapi waktu_masuk > jam masuk di tabel shift
        $terlambatHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
            $q->whereDate('tgl_absensi', $today);
        })
            ->join('shift_absen', 'detil_harian.id_shift', '=', 'shift_absen.id')
            ->where('status_kehadiran', 1)
            ->whereColumn('detil_harian.waktu_masuk', '>', 'shift_absen.waktu_masuk')
            ->count();

        $kehadiranTerbaru = Detil_Harian::with(['absensi.pekerja', 'shiftAbsen'])
            ->whereHas('absensi', function ($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })
            ->whereIn('status_kehadiran', [1, 2]) // Ambil status Hadir (1) dan Cuti (2)
            ->orderBy('updated_at', 'desc') // Menggunakan updated_at agar data terbaru (termasuk input cuti) muncul di atas
            ->limit(5)
            ->get();

        $boronganTerbaru = Detil_Borongan::with(['absensi.pekerja'])
            ->whereHas('absensi', function ($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })
            ->where('status_kehadiran', 1) // Ambil status Hadir (1) dan Cuti (2)
            ->orderBy('updated_at', 'desc') // Menggunakan updated_at agar data terbaru (termasuk input cuti) muncul di atas
            ->limit(5)
            ->get();
        // dd($boronganTerbaru);

        $today = now()->startOfDay();
        $thirtyDaysLater = now()->addDays(30)->endOfDay();

        $urgentKontrak = PKWT::with('pekerja', 'unit')
            ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
            ->orderBy('tgl_akhir_pkwt', 'asc')
            ->first();

        // --- 1. PKWT EXPIRED (Sudah lewat tanggal tapi status_aktif masih 1) ---
$expiredKontrakList = PKWT::with(['pekerja', 'unit'])
    ->whereHas('pekerja', function($q) {
        $q->where('status_aktif', 1);
    })
    ->where('tgl_akhir_pkwt', '<', $today)
    ->orderBy('tgl_akhir_pkwt', 'asc')
    ->get();

$urgentExpiredKontrak = $expiredKontrakList->first();
$totalExpiredKontrak = $expiredKontrakList->count();
$othersExpiredKontrak = $expiredKontrakList->skip(1);
$lewatHariKontrak = $urgentExpiredKontrak ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredKontrak->tgl_akhir_pkwt), false)) : 0;

// --- 2. PKWT AKAN BERAKHIR (Logika existing Anda) ---
$kontrakMendekatiList = PKWT::with(['pekerja', 'unit'])
    ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
    ->orderBy('tgl_akhir_pkwt', 'asc')
    ->get();

$urgentKontrak = $kontrakMendekatiList->first();
$totalKontrakMendekati = $kontrakMendekatiList->count();
$othersKontrak = $kontrakMendekatiList->skip(1);
// Hitung sisa hari untuk yang urgent
$sisaHari = $urgentKontrak ? Carbon::today()->diffInDays(Carbon::parse($urgentKontrak->tgl_akhir_pkwt), false) : 0;

        // 1. MITRA EXPIRED (Sudah lewat tapi masih aktif)
        $mitraExpiredList = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->where('tgl_akhir_mou', '<', $today)
            ->orderBy('tgl_akhir_mou', 'asc')
            ->get();

        $urgentExpiredMitra = $mitraExpiredList->first();
        $totalExpiredMitra = $mitraExpiredList->count();
        $othersExpiredMitra = $mitraExpiredList->skip(1);

        // 2. MITRA MENDEKATI HABIS (Dalam 30 hari ke depan)
        $mitraMendekatiList = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->whereBetween('tgl_akhir_mou', [$today, $thirtyDaysLater])
            ->orderBy('tgl_akhir_mou', 'asc')
            ->get();

        $urgentMitra = $mitraMendekatiList->first();
        $totalMitraMendekati = $mitraMendekatiList->count();
        $othersMitra = $mitraMendekatiList->skip(1);

        // --- HITUNG SISA HARI (Aman untuk UI) ---
        $sisaHariMitra = $urgentMitra
            ? Carbon::today()->diffInDays(Carbon::parse($urgentMitra->tgl_akhir_mou), false)
            : 0;

        $lewatHariMitra = $urgentExpiredMitra
            ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredMitra->tgl_akhir_mou), false))
            : 0;

        // 2. Hitung total kontrak yang memenuhi kriteria (untuk angka +X lainnya)
        $totalKontrakMendekati = PKWT::whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])->count();

        // Ambil semua data kontrak yang mendekati (Misal limit 5 untuk performa)
        $kontrakMendekatiList = PKWT::with('pekerja')
            ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
            ->orderBy('tgl_akhir_pkwt', 'asc')
            ->get();

        $urgentKontrak = $kontrakMendekatiList->first();
        $totalKontrakMendekati = $kontrakMendekatiList->count();
        // Sisanya (untuk list dropdown)
        $othersKontrak = $kontrakMendekatiList->skip(1);

        $absensiPendingCount = Absensi::where('verifikasi', 0)->count();

        $penilaianTerbaru = Penilaian_Pkwt::with(['pekerja', 'unit']) // Eager load relasi
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $penilaianPending = Penilaian_Pkwt::with(['pekerja', 'unit'])
            ->where('status_hrd', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'dashboard',
            [
                'employeeChartData' => $monthlyData,
                'selectedYear' => $year,
                'kehadiranTerbaru' => $kehadiranTerbaru,
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
            ],
            compact(
                'totalPekerja',
                'totalMitra',
                'pegawaiBulanIni',
                'mitraMendekati',
                'hadirHariIni', // Pass ke View
                'izinSakitHariIni', // Pass ke View
                'terlambatHariIni', // Pass ke View
            ),
        );
    }

    public function verifyPenilaianHrd($id)
    {
        try {
            // 1. Cari data penilaian berdasarkan ID
            $penilaian = Penilaian_Pkwt::findOrFail($id);
            
            $User = User::where('id', auth()->id())->first();

            // dd($User);

            if ($User->role == 'head_supervisor') {
                $penilaian->update([
                    'status_staff' => auth()->id(),
                    'updated_by'   => auth()->id(), 
                ]);
            } elseif ($User->role == 'hrd') {
                $penilaian->update([
                    'status_hrd' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            // 2. Update status_hrd dan catat siapa yang mengupdate
            

            // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
            return back()->with('success', 'Penilaian untuk ' . $penilaian->pekerja->nama . ' berhasil diverifikasi.');
        } catch (\Exception $e) {
            // Tangani jika terjadi error
            return back()->with('error', 'Gagal memverifikasi penilaian: ' . $e->getMessage());
        }
    }
}
