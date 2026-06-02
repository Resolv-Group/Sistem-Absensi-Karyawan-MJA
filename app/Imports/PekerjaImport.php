<?php

namespace App\Imports;

use App\Models\Pekerja;
use App\Models\PKWT; // ⬅️ TAMBAHKAN INI UNTUK MEMANGGIL MODEL PKWT
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PekerjaImport implements ToModel, WithHeadingRow
{
    /**
     * Memberitahu sistem bahwa judul kolom (header) ada di baris ke-4.
     */
    public function headingRow(): int
    {
        return 4; 
    }

    public function model(array $row)
    {
        // Abaikan baris jika NIK kosong (mencegah error membaca baris kosong di ujung file)
        if (empty($row['nik'])) {
            return null;
        }

        // Proses parsing tanggal
        $tanggalLahir     = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tanggalBergabung = $this->parseDate($row['tanggal_bergabung'] ?? null);
        $tanggalResign    = $this->parseDate($row['tanggal_resign'] ?? null);
        
        // =========================================================================
        // 1. BUAT/UPDATE DATA PEKERJA (Simpan ke Variabel $pekerja)
        // =========================================================================
        $pekerja = Pekerja::updateOrCreate(
            ['nik' => $row['nik']], // Kunci Pencarian Utama (Berdasarkan NIK)
            [
                'id_pekerja'         => $row['id_pekerja'] ?? null,
                'nama'               => $row['nama_lengkap'] ?? null,
                'kpj'                => $row['bpjs_ketenagakerjaan'] ?? null, 
                'naker'              => $row['bpjs_kesehatan'] ?? null,       
                'no_kk'              => $row['nomor_kk'] ?? null,
                'tempat_lahir'       => $row['tempat_lahir'] ?? null,
                'tgl_lahir'          => $tanggalLahir,
                'kelamin'            => $row['jenis_kelamin'] ?? null,
                'pendidikan'         => $row['pendidikan'] ?? null,
                'status_kawin'       => $row['status_perkawinan'] ?? null,
                'anak'               => $row['jumlah_anak'] ?? 0,
                'tgl_bergabung'      => $tanggalBergabung,
                'tgl_resign'         => $tanggalResign,
                
                // Status Aktif Otomatis: Jika tgl_resign kosong, berarti status aktif (1). Jika ada isinya, tidak aktif (0).
                'status_aktif'       => 1,

                // Data Alamat
                'alamat'             => $row['jalannama_gedung'] ?? null, 
                'desa'               => $row['kelurahandesa'] ?? null,
                'rt'                 => $row['rt'] ?? null,
                'rw'                 => $row['rw'] ?? null,
                'kota'               => $row['kotakabupaten'] ?? null,
                'kecamatan'          => $row['kecamatan'] ?? null,
                'provinsi'           => $row['provinsi'] ?? null,
                
                // Kontak & Rekening
                'email'              => $row['email_pribadi'] ?? null,
                'telp'               => $row['nomor_telepon_pribadi'] ?? null,
                'nama_rek'           => $row['nama_bank'] ?? null, 
                'rekening'           => $row['no_rekening'] ?? null,
                
                // Kontak Darurat
                'nama_emergency'     => $row['nama_kontak_emergency'] ?? null,
                'telp_emergency'     => $row['nomor_telepon_darurat'] ?? null,
                'hubungan_emergency' => $row['hubungan'] ?? null,
                'ibu_kandung'        => $row['ibu_kandung'] ?? null,
            ]
        );

        // =========================================================================
        // 2. PROSES 50 KOLOM PKWT OTOMATIS (Simpan ke Tabel PKWT)
        // =========================================================================
        for ($i = 1; $i <= 50; $i++) {
            // Di Excel judulnya "PKWT TGL MASUK 1", oleh Laravel otomatis dibaca "pkwt_tgl_masuk_1"
            $keyMasuk  = 'pkwt_tgl_masuk_' . $i;
            $keyKeluar = 'pkwt_tgl_keluar_' . $i;

            // Jika kolom masuk dan keluar di Excel ada isinya
            if (!empty($row[$keyMasuk]) && !empty($row[$keyKeluar])) {
                
                $parsedMasuk  = $this->parseDate($row[$keyMasuk]);
                $parsedKeluar = $this->parseDate($row[$keyKeluar]);

                // Jika format tanggal valid dan berhasil diproses
                if ($parsedMasuk && $parsedKeluar) {
                    PKWT::updateOrCreate(
                        [
                            // Syarat unik: Cari apakah PKWT di tanggal ini untuk pekerja ini sudah ada?
                            // Kita pakai $pekerja->id_pekerja (Jika ada) atau $pekerja->id 
                            'id_pekerja'     => $pekerja->id_pekerja ?? $pekerja->id, 
                            'tgl_mulai_pkwt' => $parsedMasuk,
                            'tgl_akhir_pkwt' => $parsedKeluar,
                        ],
                        [
                            // Jika belum ada, buat baru dengan status_aktif = 0 (History)
                            // Jika dokumen dan unit tidak ada di excel, akan otomatis null (aman)
                            'status_aktif'   => 0, 
                        ]
                    );
                }
            }
        }

        // Kembalikan objek pekerja agar proses ToModel selesai dengan baik
        return $pekerja;
    }

    /**
     * Fungsi untuk memastikan format tanggal masuk ke database (Y-m-d)
     */
    private function parseDate($value)
    {
        if (!$value) return null;
        
        try {
            // 1. Cek jika formatnya adalah "Date" serial angka bawaan Excel (misal: 44000)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            
            // 2. Coba parsing normal (Untuk YYYY-MM-DD)
            return Carbon::parse($value)->format('Y-m-d');
            
        } catch (\Exception $e) {
            // 3. JIKA GAGAL (KARENA FORMAT YYYY-DD-MM SEPERTI "1990-23-05")
            try {
                // Paksa sistem untuk membaca dengan format Tahun-Tanggal-Bulan
                return Carbon::createFromFormat('Y-d-m', trim($value))->format('Y-m-d');
            } catch (\Exception $e2) {
                // Jika masih gagal (mungkin isian teks ngawur), kembalikan null
                return null; 
            }
        }
    }
}