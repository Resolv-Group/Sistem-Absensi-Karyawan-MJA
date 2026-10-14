<?php

namespace App\Exports;

use App\Models\Unit;
use App\Models\PKWT;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\Log;

class PekerjaUnitExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $unit_id;
    protected $nama_unit;
    protected $status_angka = [];

    // Menangkap ID unit dan array status dari controller
    public function __construct($unit_id, $statusArray = ['aktif'])
    {
        $this->unit_id = $unit_id;
        $unit = Unit::findOrFail($unit_id);
        $this->nama_unit = $unit->nama_unit;    
        
        // Konversi nilai checkbox ('aktif', 'non-aktif', 'pending') menjadi angka untuk database
        foreach ($statusArray as $status) {
            if ($status === 'aktif') {
                $this->status_angka[] = 1;
            } elseif ($status === 'non-aktif') {
                $this->status_angka[] = 0;
            } elseif ($status === 'pending') {
                $this->status_angka[] = 2;
            }
        }
        
        // Default (fallback) jika entah mengapa array kosong, tampilkan yang aktif saja
        if (empty($this->status_angka)) {
            $this->status_angka = [1];
        }
        Log::info('2. Hasil Konversi Status di Export:', [
            'input_status' => $statusArray,
            'hasil_angka' => $this->status_angka
        ]);
    }

    /**
    * Mengambil data PkwtPekerja berdasarkan filter status, beserta data pekerjanya
    */
    public function collection()
    {
        $data = PKWT::with('pekerja')
            ->where('id_unit', $this->unit_id)
            ->whereIn('status_aktif', $this->status_angka)
            ->get();

        // --- LOGGING COLLECTION ---
        Log::info('3. Hasil Query Database:', [
            'query_pakai_status' => $this->status_angka,
            'jumlah_data_ditemukan' => $data->count(),
            'contoh_id_pkwt_ditemukan' => $data->pluck('id')->toArray()
        ]);

        return $data->pluck('pekerja')->filter();
    }

    /**
    * Memetakan data apa saja yang masuk ke baris Excel
    */
    public function map($pekerja): array
    {
        $kelamin = $pekerja->kelamin;

        // Jika berbentuk objek Enum Laravel, ambil properti ->value-nya
        if (is_object($kelamin) && property_exists($kelamin, 'value')) {
            $kelaminValue = $kelamin->value;
        } else {
            $kelaminValue = $kelamin;
        }

        // Cek apakah nilainya 1 (Laki-laki) atau lainnya (Perempuan)
        $jenisKelaminText = ($kelaminValue == 1) ? 'Laki-laki' : 'Perempuan';

        return [
            $pekerja->id_pekerja,
            $pekerja->nama,
            
            // Tambahkan kutip tunggal di depan data angka yang panjang
            "'" . $pekerja->nik,
            "'" . $pekerja->no_kk,
            
            $jenisKelaminText,
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