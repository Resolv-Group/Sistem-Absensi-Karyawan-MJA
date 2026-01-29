<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Detil_Harian;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
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
            $hadirHariIni = Detil_Harian::whereHas('absensi', function($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })->where('status_kehadiran', 1)->count();

            // 2. Izin / Sakit Hari Ini (Status 2)
            $izinSakitHariIni = Detil_Harian::whereHas('absensi', function($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })->where('status_kehadiran', 2)->count();

            // 3. Terlambat Hari Ini
            // Menghitung yang hadir (status 1) tapi waktu_masuk > jam masuk di tabel shift
            $terlambatHariIni = Detil_Harian::whereHas('absensi', function($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })
            ->join('shift_absen', 'detil_harian.id_shift', '=', 'shift_absen.id')
            ->where('status_kehadiran', 1)
            ->whereColumn('detil_harian.waktu_masuk', '>', 'shift_absen.waktu_masuk')
            ->count();

            $kehadiranTerbaru = Detil_Harian::with(['absensi.pekerja', 'shiftAbsen'])
                ->whereHas('absensi', function($q) use ($today) {
                    $q->whereDate('tgl_absensi', $today);
                })
                ->whereIn('status_kehadiran', [1, 2]) // Ambil status Hadir (1) dan Cuti (2)
                ->orderBy('updated_at', 'desc') // Menggunakan updated_at agar data terbaru (termasuk input cuti) muncul di atas
                ->limit(5)
                ->get();

            $today = now()->startOfDay();
            $thirtyDaysLater = now()->addDays(30)->endOfDay();

            $urgentKontrak = PKWT::with('pekerja')
                ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
                ->orderBy('tgl_akhir_pkwt', 'asc')
                ->first();

            // 2. Hitung total kontrak yang memenuhi kriteria (untuk angka +X lainnya)
            $totalKontrakMendekati = PKWT::whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
                ->count();

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
                'penilaianTerbaru' => $penilaianTerbaru,
                'penilaianPending' => $penilaianPending,
                'urgentKontrak' => $urgentKontrak,
                'totalKontrakMendekati' => $totalKontrakMendekati,
                'absensiPendingCount' => $absensiPendingCount,
                'othersKontrak' => $othersKontrak
            ],
            compact(
                'totalPekerja',
                'totalMitra',
                'pegawaiBulanIni',
                'mitraMendekati',
                'hadirHariIni',      // Pass ke View
                'izinSakitHariIni',  // Pass ke View
                'terlambatHariIni'   // Pass ke View
            ),
        );
    }

    public function verifyPenilaianHrd($id)
    {
        try {
            // 1. Cari data penilaian berdasarkan ID
            $penilaian = Penilaian_Pkwt::findOrFail($id);

            // 2. Update status_hrd dan catat siapa yang mengupdate
            $penilaian->update([
                'status_hrd' => 1,
                'updated_by' => auth()->id(), // Mencatat ID user yang melakukan verifikasi
            ]);

            // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
            return back()->with('success', 'Penilaian untuk ' . $penilaian->pekerja->nama . ' berhasil diverifikasi.');

        } catch (\Exception $e) {
            // Tangani jika terjadi error
            return back()->with('error', 'Gagal memverifikasi penilaian: ' . $e->getMessage());
        }
    }
}
