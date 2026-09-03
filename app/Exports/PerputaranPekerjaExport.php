<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Unit;
use App\Models\Divisi;
use App\Models\PKWT;
use Carbon\Carbon;

class PerputaranPekerjaExport implements FromView, ShouldAutoSize
{
    protected $unitId;
    protected $bulanTahun;

    public function __construct($unitId, $bulanTahun)
    {
        $this->unitId = $unitId;
        $this->bulanTahun = $bulanTahun;
    }

    public function view(): View
    {
        Carbon::setLocale('id');

        $unit = Unit::find($this->unitId);
        $baseDate = Carbon::createFromFormat('Y-m', $this->bulanTahun);

        $startPeriode = $baseDate->copy()->startOfMonth();
        $endPeriode   = $baseDate->copy()->endOfMonth();
        
        $periodeLabel = $startPeriode->translatedFormat('d F Y') . ' s/d ' . $endPeriode->translatedFormat('d F Y');

        $divisis = Divisi::all(); 
        $rekapData = [];

        foreach ($divisis as $divisi) {
        $awal = PKWT::where('id_unit', $this->unitId)
                ->where('divisi_id', $divisi->id)
                ->where('status_aktif', 1)
                ->where('created_at', '<', $startPeriode)
                ->count();

            // Menghitung pembaruan/data baru (dibuat di dalam rentang periode dan status aktif 1)
            $pembaruan = PKWT::where('id_unit', $this->unitId)
                ->where('divisi_id', $divisi->id)
                ->where('status_aktif', 1)
                ->whereBetween('created_at', [$startPeriode, $endPeriode])
                ->count();

            $pengurangan = PKWT::where('id_unit', $this->unitId)
                ->where('divisi_id', $divisi->id)
                ->where('status_aktif', 0)
                ->whereMonth('updated_at', $baseDate->month)
                ->whereYear('updated_at', $baseDate->year)
                ->count();

            if ($awal > 0 || $pembaruan > 0 || $pengurangan > 0) {
                $rekapData[] = [
                    'divisi'      => $divisi->nama,
                    'awal'        => $awal,
                    'pembaruan'   => $pembaruan,
                    'pengurangan' => $pengurangan,
                    'akhir'       => ($awal + $pembaruan) - $pengurangan
                ];
            }
        }

        return view('Exports.perputaran_pekerja', [
            'unit'         => $unit,
            'periodeLabel' => $periodeLabel,
            'rekapData'    => $rekapData
        ]);
    }
}