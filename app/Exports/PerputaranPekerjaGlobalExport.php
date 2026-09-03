<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Unit;
use App\Models\PKWT;
use Carbon\Carbon;

class PerputaranPekerjaGlobalExport implements FromView, ShouldAutoSize
{
    protected $bulanTahun;

    public function __construct($bulanTahun)
    {
        $this->bulanTahun = $bulanTahun;
    }

    public function view(): View
    {
        Carbon::setLocale('id');

        $baseDate = Carbon::createFromFormat('Y-m', $this->bulanTahun);
        $startPeriode = $baseDate->copy()->startOfMonth();
        $endPeriode   = $baseDate->copy()->endOfMonth();
        
        $periodeLabel = $startPeriode->translatedFormat('d F Y') . ' s/d ' . $endPeriode->translatedFormat('d F Y');

        $units = Unit::all();
        $rekapData = [];

        foreach ($units as $unit) {
            // Menghitung total awal per unit
            $awal = PKWT::where('id_unit', $unit->id)
                ->where('status_aktif', 1)
                ->where('created_at', '<', $startPeriode)
                ->count();

            // Menghitung total pembaruan per unit
            $pembaruan = PKWT::where('id_unit', $unit->id)
                ->where('status_aktif', 1)
                ->whereBetween('created_at', [$startPeriode, $endPeriode])
                ->count();

            // Menghitung total pengurangan per unit
            $pengurangan = PKWT::where('id_unit', $unit->id)
                ->where('status_aktif', 0)
                ->whereMonth('updated_at', $baseDate->month)
                ->whereYear('updated_at', $baseDate->year)
                ->count();

            // Masukkan ke rekap jika unit memiliki data karyawan
            if ($awal > 0 || $pembaruan > 0 || $pengurangan > 0) {
                $rekapData[] = [
                    'nama_unit'   => $unit->nama_unit ?? '-',
                    'awal'        => $awal,
                    'pembaruan'   => $pembaruan,
                    'pengurangan' => $pengurangan,
                    'akhir'       => ($awal + $pembaruan) - $pengurangan
                ];
            }
        }

        return view('exports.perputaran_pekerja_global', [
            'periodeLabel' => $periodeLabel,
            'rekapData'    => $rekapData
        ]);
    }
}