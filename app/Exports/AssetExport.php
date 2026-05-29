<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetExport implements FromView, ShouldAutoSize
{
    protected $dataAsset;

    public function __construct($dataAsset)
    {
        $this->dataAsset = $dataAsset;
    }

    public function view(): View
    {
        return view('Exports.asset', [
            'assets' => $this->dataAsset,
        ]);
    }
}
