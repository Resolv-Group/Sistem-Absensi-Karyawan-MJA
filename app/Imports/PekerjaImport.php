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
    private $idPrefix;
    private $currentIdCounter;

    private function pick($value, $fallback = null)
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        // filled() bawaan Laravel: angka 0 dan '0' dianggap ADA isinya (true), hanya null/'' yang false
        return filled($value) ? $value : $fallback;
    }

    public function __construct()
    {
        // 1. Buat prefix berdasarkan hari ini: MJA + dmy (Contoh: MJA040625)
        // d = Tanggal (2 digit), m = Bulan (2 digit), y = Tahun (2 digit)
        $this->idPrefix = 'MJA' . now()->format('dmy');
        
        // 2. Cari data pekerja terakhir di database yang diinput pada HARI INI
        $lastPekerja = Pekerja::where('id_pekerja', 'like', $this->idPrefix . '%')
            ->orderBy('id_pekerja', 'desc')
            ->first();

        // 3. Jika ada, ambil 3 angka terakhirnya. Jika tidak ada, mulai dari 0.
        if ($lastPekerja) {
            $lastNumber = intval(substr($lastPekerja->id_pekerja, -3));
            $this->currentIdCounter = $lastNumber;
        } else {
            $this->currentIdCounter = 0;
        }
    }

    /**
     * Fungsi untuk mencetak ID baru dengan menambahkan urutan (001, 002, dst)
     */
    private function generateIdPekerja()
    {
        $this->currentIdCounter++;
        // Format angka menjadi 3 digit (contoh: 1 menjadi 001)
        return $this->idPrefix . str_pad($this->currentIdCounter, 3, '0', STR_PAD_LEFT);
    }


    /**
     * Memberitahu sistem bahwa judul kolom (header) ada di baris ke-4.
     */
    public function headingRow(): int
    {
        return 4; 
    }

    public function model(array $row)
    {
        // =====================================================================
        // AUTO CLEANER NIK: Hapus spasi dan tanda petik tunggal (') di awal NIK
        // =====================================================================
        $nikBersih = isset($row['nik']) ? trim(ltrim($row['nik'], "'")) : null;

        // Abaikan baris jika NIK kosong (mencegah error membaca baris kosong di ujung file)
        if (empty($nikBersih)) {
            return null;
        }

        // Proses parsing tanggal
        $tanggalLahir     = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tanggalBergabung = $this->parseDate($row['tanggal_bergabung'] ?? null);
        $tanggalResign    = $this->parseDate($row['tanggal_resign'] ?? null);

        // Cek data pekerja di database menggunakan NIK yang sudah dibersihkan
        $pekerjaExisting = Pekerja::where('nik', $nikBersih)->first();
        
        // =========================================================================
        // LOGIKA PENENTUAN ID PEKERJA
        // =========================================================================
        if (!empty($row['id_pekerja'])) {
            // Skenario 1: Jika di Excel ada isinya, wajib pakai yang dari Excel
            $idPekerja = $row['id_pekerja'];
        } elseif ($pekerjaExisting && !empty($pekerjaExisting->id_pekerja)) {
            // Skenario 2: Jika di Excel kosong, TAPI di database sudah pernah punya ID, pakai ID lamanya
            $idPekerja = $pekerjaExisting->id_pekerja;
        } else {
            // Skenario 3: Jika di Excel kosong DAN ini adalah pekerja baru, buatkan ID MJA otomatis
            $idPekerja = $this->generateIdPekerja();
        }
            
        // =========================================================================
        // 1. BUAT/UPDATE DATA PEKERJA (Simpan ke Variabel $pekerja)
        // =========================================================================
        // =========================================================================
        // 1. BUAT/UPDATE DATA PEKERJA (Simpan ke Variabel $pekerja)
        // =========================================================================
        $pekerja = Pekerja::updateOrCreate(
            ['nik' => $nikBersih],
            [
                'id_pekerja'         => $idPekerja,
                'nama'               => $this->pick($row['nama_lengkap'] ?? null, $pekerjaExisting->nama ?? null),
                'kpj'                => $this->pick($row['bpjs_ketenagakerjaan'] ?? null, $pekerjaExisting->kpj ?? null),
                'naker'              => $this->pick($row['bpjs_kesehatan'] ?? null, $pekerjaExisting->naker ?? null),
                'no_kk'              => $this->pick($row['nomor_kk'] ?? null, $pekerjaExisting->no_kk ?? null),
                'tempat_lahir'       => $this->pick($row['tempat_lahir'] ?? null, $pekerjaExisting->tempat_lahir ?? null),
                'tgl_lahir'          => $tanggalLahir ?: ($pekerjaExisting->tgl_lahir ?? null),
                'kelamin'            => $this->pick($row['jenis_kelamin'] ?? null, $pekerjaExisting->kelamin ?? null), // ⬅️ Angka 0 sekarang aman!
                'pendidikan'         => $this->pick($row['pendidikan'] ?? null, $pekerjaExisting->pendidikan ?? null),
                'status_kawin'       => $this->pick($row['status_perkawinan'] ?? null, $pekerjaExisting->status_kawin ?? null), // ⬅️ ' TK' otomatis jadi 'TK'
                'anak'               => $this->pick($row['jumlah_anak'] ?? null, $pekerjaExisting->anak ?? 0),
                'tgl_bergabung'      => $tanggalBergabung ?: ($pekerjaExisting->tgl_bergabung ?? null),
                'tgl_resign'         => $tanggalResign ?: ($pekerjaExisting->tgl_resign ?? null),
                'status_aktif'       => empty($tanggalResign) ? 1 : 0,

                // Data Alamat
                'alamat'             => $this->pick($row['jalannama_gedung'] ?? null, $pekerjaExisting->alamat ?? null),
                'desa'               => $this->pick($row['kelurahandesa'] ?? null, $pekerjaExisting->desa ?? null),
                'rt'                 => $this->pick($row['rt'] ?? null, $pekerjaExisting->rt ?? null),
                'rw'                 => $this->pick($row['rw'] ?? null, $pekerjaExisting->rw ?? null), // ⬅️ RW 0 juga aman!
                'kota'               => $this->pick($row['kotakabupaten'] ?? null, $pekerjaExisting->kota ?? null),
                'kecamatan'          => $this->pick($row['kecamatan'] ?? null, $pekerjaExisting->kecamatan ?? null),
                'provinsi'           => $this->pick($row['provinsi'] ?? null, $pekerjaExisting->provinsi ?? null),

                // Kontak & Rekening
                'email'              => $this->pick($row['email_pribadi'] ?? null, $pekerjaExisting->email ?? null),
                'telp'               => $this->pick($row['nomor_telepon_pribadi'] ?? null, $pekerjaExisting->telp ?? null),
                'nama_rek'           => $this->pick($row['nama_bank'] ?? null, $pekerjaExisting->nama_rek ?? null),
                'rekening'           => $this->pick(
                    isset($row['no_rekening']) ? str_replace(' ', '', $row['no_rekening']) : null, // ⬅️ Opsional: bersihkan spasi no rekening
                    $pekerjaExisting->rekening ?? null
                ),

                // Kontak Darurat
                'nama_emergency'     => $this->pick($row['nama_kontak_emergency'] ?? null, $pekerjaExisting->nama_emergency ?? null),
                'telp_emergency'     => $this->pick($row['nomor_telepon_darurat'] ?? null, $pekerjaExisting->telp_emergency ?? null),
                'hubungan_emergency' => $this->pick($row['hubungan'] ?? null, $pekerjaExisting->hubungan_emergency ?? null),
                'ibu_kandung'        => $this->pick($row['ibu_kandung'] ?? null, $pekerjaExisting->ibu_kandung ?? null),
            ]
        );

        // =========================================================================
        // 2. PROSES 50 KOLOM PKWT OTOMATIS (Simpan ke Tabel PKWT)
        // =========================================================================
        for ($i = 1; $i <= 50; $i++) {
            
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
                            'id_pekerja'     => $pekerja->id, 
                            'tgl_mulai_pkwt' => $parsedMasuk,
                            'tgl_akhir_pkwt' => $parsedKeluar,
                        ],
                        [
                            // Jika belum ada, buat baru dengan status history (0)
                             
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
    /**
     * AUTO CONVERTER TANGGAL: Mengubah segala bentuk tanggal berantakan menjadi Y-m-d (contoh: 2025-03-15)
     */
    private function parseDate($value)
    {
        // Jika kosong, langsung kembalikan null
        if (empty($value)) return null;
        
        $value = trim($value);

        try {
            // 1. CEK ANGKA SERIAL EXCEL (Contoh hasil VLOOKUP atau copy-paste yang terbaca sebagai angka seperti 44000)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // 2. TRANSLATOR BAHASA INDONESIA KE INGGRIS
            // Carbon hanya paham bahasa Inggris, jadi kita paksa ubah teks Indonesia ke Inggris
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

            // Ganti kata-katanya
            $valueTranslated = str_ireplace($bulanIndo, $bulanInggris, $value);

            // Bersihkan spasi ganda jika ada yang mengetik "07  April  2026"
            $valueTranslated = preg_replace('/\s+/', ' ', $valueTranslated);

            // 3. PARSING OTOMATIS (Akan memproses format seperti 2020-08-10, 07 April 2026, dll)
            return Carbon::parse($valueTranslated)->format('Y-m-d');

        } catch (\Exception $e) {
            // 4. PLAN B: Tangani garis miring/strip ganda (seperti 11//07/2004) dan format DD/MM/YYYY
            try {
                $valueCleaned = preg_replace('/[\/\-]+/', '-', $value);
                return Carbon::createFromFormat('d-m-Y', $valueCleaned)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}