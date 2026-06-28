<?php

namespace App\Imports;

use App\Models\MitraKerja;
use App\Models\BidangUsaha;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MitraKerjaImport implements ToModel, WithHeadingRow
{
    /**
     * Memberitahu sistem bahwa judul kolom (header) di file Excel
     * untuk mitra kerja ini berada di baris ke-3.
     */
    public function headingRow(): int
    {
        return 3; 
    }

    public function model(array $row)
    {
        // Abaikan baris jika nama perusahaan kosong
        if (empty($row['nama_perusahaan'])) {
            return null;
        }

        // =========================================================================
        // 1. LOGIKA AUTO-ASSIGN BIDANG USAHA (RELASI)
        // =========================================================================
        $bidangUsahaId = null;
        if (!empty($row['bidang_usaha'])) {
            $namaBidang = ucwords(strtolower(trim($row['bidang_usaha'])));

            $bidangUsaha = BidangUsaha::firstOrCreate(
                ['nama' => $namaBidang]
            );

            $bidangUsahaId = $bidangUsaha->id;
        }

        // =========================================================================
        // 2. PARSING TANGGAL & STATUS
        // =========================================================================
        $tglMulai = $this->parseDate($row['mulai_kerja_sama'] ?? null);
        $tglAkhir = $this->parseDate($row['berakhir_mou'] ?? null);

        $statusAktif = 1; 
        if (!empty($row['status_mou']) && strtolower(trim($row['status_mou'])) === 'tidak aktif') {
            $statusAktif = 0;
        }

        // =========================================================================
        // 3. INSERT ATAU UPDATE KE TABEL MITRA KERJA
        // =========================================================================
        return MitraKerja::updateOrCreate(
            ['nama_mitra' => trim($row['nama_perusahaan'])], 
            [
                'bidang_usaha_id'     => $bidangUsahaId, 
                'pimpinan'            => $row['nama_pimpinan'] ?? null,
                'telp_perusahaan'     => $row['no_telp'] ?? null,
                'status_pajak'        => $row['status_pajak'] ?? null,
                
                // KOTA DAN ALAMAT SEKARANG TERPISAH
                'alamat'              => $row['alamat'] ?? null,
                'kota'                => $row['kota'] ?? null,
                
                'tgl_mulai_kerjasama' => $tglMulai,
                'tgl_akhir_mou'       => $tglAkhir,
                'status_mou'          => $row['status_mou'] ?? null,
                'status_aktif'        => $statusAktif,
            ]
        );
    }

    /**
     * AUTO CONVERTER TANGGAL
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;
        
        $value = trim($value);

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            $bulanIndo = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            $bulanInggris = [
                'January', 'February', 'March', 'April', 'May', 'June', 
                'July', 'August', 'September', 'October', 'November', 'December',
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];

            $valueTranslated = str_ireplace($bulanIndo, $bulanInggris, $value);
            $valueTranslated = preg_replace('/\s+/', ' ', $valueTranslated);

            return Carbon::parse($valueTranslated)->format('Y-m-d');

        } catch (\Exception $e) {
            try {
                $valueCleaned = str_replace('/', '-', $value);
                return Carbon::createFromFormat('d-m-Y', $valueCleaned)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}