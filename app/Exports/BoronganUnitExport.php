<?php

namespace App\Exports;

use App\Models\Unit;
use App\Models\Borongan; // Sesuaikan jika nama model Anda berbeda
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class BoronganUnitExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $unit_id;
    protected $nama_unit;

    public function __construct($unit_id)
    {
        $this->unit_id = $unit_id;
        $unit = Unit::findOrFail($unit_id);
        $this->nama_unit = $unit->nama_unit;
    }

    /**
    * Mengambil data Borongan yang aktif di unit ini beserta relasi Kategorinya
    */
    public function collection()
    {
        return Borongan::with('dataKategori')
            ->where('id_unit', $this->unit_id)
            ->where('status_aktif', 1) // Hanya ambil yang berstatus aktif
            ->get();
    }

    /**
    * Memetakan data ke dalam baris Excel (status_aktif tidak dimasukkan)
    */
    public function map($borongan): array
    {
        return [
            $borongan->nama_item,
            $borongan->dataKategori->nama ?? 'Tanpa Kategori', // Mengambil nama dari tabel Kategori
            $borongan->harga_unit,
            $borongan->harga_pekerja,
            $borongan->satuan,
            $borongan->max_rej_subkon,
        ];
    }

    /**
    * Judul Header Excel
    */
    public function headings(): array
    {
        return [
            'Nama Item', 
            'Kategori', 
            'Harga Unit', 
            'Harga Pekerja', 
            'Satuan', 
            'Max Rej Subkon'
        ];
    }

    /**
    * Nama Sheet Excel
    */
    public function title(): string
    {
        return substr('Borongan ' . $this->nama_unit, 0, 31); 
    }
}