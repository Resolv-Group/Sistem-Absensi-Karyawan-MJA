<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KasKecilExport implements FromView, ShouldAutoSize
{
    protected $dataKasKecil;
    protected $diajukan;
    protected $diperiksa;
    protected $disetujui;

    // Menangkap data dari Controller
    public function __construct($dataKasKecil, $diajukan, $diperiksa, $disetujui)
    {
        $this->dataKasKecil = $dataKasKecil;
        $this->diajukan = $diajukan;
        $this->diperiksa = $diperiksa;
        $this->disetujui = $disetujui;
    }

    public function view(): View
    {
        // Melempar data ke blade untuk di-render menjadi Excel
        return view('exports.kas_kecil', [
            'data'      => $this->dataKasKecil,
            'diajukan'  => $this->diajukan,
            'diperiksa' => $this->diperiksa,
            'disetujui' => $this->disetujui,
        ]);
    }
}