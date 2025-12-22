<?php

namespace App\Http\Controllers;

use App\Models\MitraKerja;
use App\Models\Pekerja;
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

        return view(
            'dashboard',
            [
                'employeeChartData' => $monthlyData,
                'selectedYear' => $year,
            ],
            compact('totalPekerja', 'totalMitra', 'pegawaiBulanIni', 'mitraMendekati'),
        );
    }
}
