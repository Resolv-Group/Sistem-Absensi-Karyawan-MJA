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
    protected $durasi;

    public function __construct($unitId, $bulanTahun, $durasi)
    {
        $this->unitId = $unitId;
        $this->bulanTahun = $bulanTahun;
        $this->durasi = $durasi;
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
                ->where('status_aktif', '!=', 'pending')
                ->where('tgl_mulai_pkwt', '<', $startPeriode)
                ->where(function($q) use ($startPeriode) {
                    $q->where('tgl_akhir_pkwt', '>=', $startPeriode)
                      ->orWhere('status_aktif', 'aktif');
                })->count();

            $pembaruan = PKWT::where('id_unit', $this->unitId)
                ->where('divisi_id', $divisi->id)
                ->where('status_aktif', '!=', 'pending')
                ->whereBetween('tgl_mulai_pkwt', [$startPeriode, $endPeriode])
                ->count();

            $pengurangan = PKWT::where('id_unit', $this->unitId)
                ->where('divisi_id', $divisi->id)
                ->where('status_aktif', 'non-aktif')
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