<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KasKecilExport implements FromView, ShouldAutoSize
{
    protected $dataKasKecil;
    protected $kepada;
    protected $pembukuan;
    protected $mengetahui;
    protected $kasir;
    protected $penerima;
    protected $catatan;

    public function __construct($dataKasKecil, $kepada, $pembukuan, $mengetahui, $kasir, $penerima, $catatan)
    {
        $this->dataKasKecil = $dataKasKecil;
        $this->kepada = $kepada;
        $this->pembukuan = $pembukuan;
        $this->mengetahui = $mengetahui;
        $this->kasir = $kasir;
        $this->penerima = $penerima;
        $this->catatan = $catatan;
    }

    public function view(): View
    {
        return view('Exports.kas_kecil', [
            'data'      => $this->dataKasKecil,
            'kepada'    => $this->kepada,
            'pembukuan' => $this->pembukuan,
            'mengetahui'=> $this->mengetahui,
            'kasir'     => $this->kasir,
            'penerima'  => $this->penerima,
            'catatan'   => $this->catatan,
        ]);
    }
}