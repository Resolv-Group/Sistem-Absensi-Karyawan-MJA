<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\PKWT;
use App\Models\Unit;
use Carbon\Carbon;

class MonitoringPkwtExport implements FromView, ShouldAutoSize
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
        
        $tanggalSetelah = $baseDate->copy()->endOfMonth()->translatedFormat('d F Y');
        
        $targetMonths = [];
        for ($i = 1; $i <= $this->durasi; $i++) {
            $monthDate = $baseDate->copy()->addMonths($i);
            $targetMonths[] = [
                'format' => $monthDate->format('Y-m'),
                'label'  => $monthDate->translatedFormat('F')
            ];
        }

        $dataPkwt = PKWT::with(['pekerja', 'divisi', 'jabatan'])
            ->where('id_unit', $this->unitId)
            ->where('status_aktif', 1)
            ->get();

        return view('Exports.pkwt_monitoring', [
            'unit'           => $unit,
            'tanggalSetelah' => $tanggalSetelah,
            'targetMonths'   => $targetMonths,
            'dataPkwt'       => $dataPkwt,
            'durasi'         => $this->durasi
        ]);
    }
}