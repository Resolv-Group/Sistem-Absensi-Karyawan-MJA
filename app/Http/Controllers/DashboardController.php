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
        $hadirHariIni = Detil_Harian::whereHas('Absensi', function ($q) use ($today) {
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

        // 3. Overtime Hari Ini
        $overtimeHariIni = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
            $q->whereDate('tgl_absensi', $today);
        })
        ->where('overtime', '>', 0)
        ->count();

        // Count total attendance for the header badge
        $totalAbsensiHarian = Detil_Harian::whereHas('absensi', function ($q) use ($today) {
            $q->whereDate('tgl_absensi', $today);
        })->count();

        $kehadiranTerbaru = Detil_Harian::with(['absensi.pekerja', 'absensi.unit'])
            ->whereHas('absensi', function ($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })
            // Including statuses: 1 (Hadir), 2 (Izin), 3 (Cuti), 4 (Sakit), 5 (Rencana Cuti), 6 (Absen)
            ->whereIn('status_kehadiran', [1, 2, 3, 4, 5, 6])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Count total attendance for the header badge
        $totalAbsensiBorongan = Detil_Borongan::whereHas('absensi', function ($q) use ($today) {
                $q->whereDate('tgl_absensi', $today);
            })
            ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
            ->distinct('absensi.id_pekerja')
            ->count('absensi.id_pekerja');

        $boronganTerbaru = Detil_Borongan::query()
            ->join('absensi', 'detil_borongan.id_absensi', '=', 'absensi.id')
            ->join('pekerja', 'absensi.id_pekerja', '=', 'pekerja.id') // Ganti 'pekerjas' jika nama tabel pekerja berbeda
            ->join('unit', 'absensi.id_unit', '=', 'unit.id_unit')    // Ganti 'units' jika nama tabel unit berbeda
            ->whereDate('absensi.tgl_absensi', $today)
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
        // dd($boronganTerbaru);

        $today = now()->startOfDay();
        $thirtyDaysLater = now()->addDays(30)->endOfDay();

        $urgentKontrak = PKWT::with('pekerja', 'unit')
            ->whereBetween('tgl_akhir_pkwt', [$today, $thirtyDaysLater])
            ->orderBy('tgl_akhir_pkwt', 'asc')
            ->first();

        // --- 1. PKWT EXPIRED (Sudah lewat tanggal tapi status_aktif masih 1) ---
        $expiredKontrakList = PKWT::with(['pekerja', 'unit'])
            ->whereHas('pekerja', function ($q) {
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
        $mitraExpiredList = MitraKerja::where('status_aktif', 1)->whereNotNull('tgl_akhir_mou')->where('tgl_akhir_mou', '<', $today)->orderBy('tgl_akhir_mou', 'asc')->get();

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
        $sisaHariMitra = $urgentMitra ? Carbon::today()->diffInDays(Carbon::parse($urgentMitra->tgl_akhir_mou), false) : 0;

        $lewatHariMitra = $urgentExpiredMitra ? abs(Carbon::today()->diffInDays(Carbon::parse($urgentExpiredMitra->tgl_akhir_mou), false)) : 0;

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

        $userRole = auth()->user()->role;
        $penilaianQuery = Penilaian_Pkwt::with(['pekerja', 'unit']);
        
        if ($userRole === 'head_supervisor') {
            $penilaianQuery->where('status_staff', 0);
        } else {
            $penilaianQuery->where('status_hrd', 0);
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
                'hadirHariIni', // Pass ke View
                'izinSakitHariIni', // Pass ke View
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
                    'updated_by' => auth()->id(),
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
