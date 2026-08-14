<?php

namespace App\Exports;

use App\Models\Unit;
use App\Models\PKWT;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PekerjaUnitExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $unit_id;
    protected $nama_unit;

    // Menangkap ID unit dari controller
    public function __construct($unit_id)
    {
        $this->unit_id = $unit_id;
        $unit = Unit::findOrFail($unit_id);
        $this->nama_unit = $unit->nama_unit;
    }

    /**
    * Mengambil data PkwtPekerja yang aktif di unit ini, beserta data pekerjanya
    */
    public function collection()
    {
        // Asumsi: Model PkwtPekerja memiliki relasi belongsTo ke Pekerja dengan nama 'pekerja'
        return PKWT::with('pekerja')
            ->where('id_unit', $this->unit_id)
            ->where('status_aktif', 1) // Hanya pekerja yang aktif
            ->get()
            ->pluck('pekerja') // Ambil data pekerjanya saja dari hasil relasi
            ->filter(); // Hilangkan nilai null jika ada data relasi yang terputus
    }

    /**
    * Memetakan data apa saja yang masuk ke baris Excel
    */
    public function map($pekerja): array
    {
        return [
            $pekerja->id_pekerja,
            $pekerja->nama,
            
            // Tambahkan kutip tunggal di depan data angka yang panjang
            "'" . $pekerja->nik,
            "'" . $pekerja->no_kk,
            
            $pekerja->kelamin == '1' ? 'Laki-laki' : 'Perempuan',
            $pekerja->tempat_lahir,
            $pekerja->tgl_lahir,
            $pekerja->pendidikan,
            $pekerja->status_kawin,
            $pekerja->anak ?? '0',
            $pekerja->alamat,
            $pekerja->rt . ' / ' . $pekerja->rw,
            $pekerja->desa,
            $pekerja->kecamatan,
            $pekerja->kota,
            $pekerja->provinsi,
            
            // Terapkan juga untuk Nomor Telepon, Rekening, dan BPJS 
            // agar angka '0' di depan tidak hilang
            "'" . $pekerja->telp,
            $pekerja->email,
            $pekerja->nama_rek,
            "'" . $pekerja->rekening,
            "'" . $pekerja->kpj,
            "'" . $pekerja->naker,
            
            $pekerja->tgl_bergabung,
            $pekerja->tgl_resign ?? '-',
            $pekerja->nama_emergency,
            $pekerja->hubungan_emergency,
            
            // Telepon Darurat
            "'" . $pekerja->telp_emergency,
            
            $pekerja->ibu_kandung,
        ];
    }

    /**
    * Judul Header (Baris Pertama Excel)
    */
    public function headings(): array
    {
        return [
            'ID Pekerja', 'Nama Lengkap', 'NIK', 'No KK', 'Jenis Kelamin', 'Tempat Lahir', 'Tgl Lahir',
            'Pendidikan', 'Status Kawin', 'Jml Anak', 'Alamat', 'RT/RW', 'Desa',
            'Kecamatan', 'Kota', 'Provinsi', 'No. Telepon', 'Email',
            'Bank', 'No Rekening', 'BPJS Ketenagakerjaan', 'BPJS Kesehatan',
            'Tgl Bergabung', 'Tgl Resign', 'Kontak Darurat',
            'Hub. Darurat', 'Telp Darurat', 'Ibu Kandung'
        ];
    }

    /**
    * Nama Sheet di bagian bawah Excel
    */
    public function title(): string
    {
        // Maksimal karakter judul sheet Excel adalah 31
        return substr('Pekerja ' . $this->nama_unit, 0, 31); 
    }
}