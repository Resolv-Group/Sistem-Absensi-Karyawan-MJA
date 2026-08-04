<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\User; // Pastikan memanggil model User
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StaffImport implements ToModel, WithHeadingRow
{
    /**
     * Judul kolom / header berada di baris ke-4 pada file template Staff Anda
     */
    public function headingRow(): int
    {
        return 4; 
    }

    public function model(array $row)
    {
        // Auto-cleaner NIK: Hapus spasi dan petik
        $nikBersih = isset($row['nik']) ? trim(ltrim($row['nik'], "'")) : null;

        // Skip baris jika NIK kosong (mencegah error membaca baris kosong di Excel)
        if (empty($nikBersih)) {
            return null;
        }

        // =========================================================================
        // 1. PARSING TANGGAL MENGGUNAKAN HELPER
        // =========================================================================
        $tglLahir     = $this->parseDate($row['tanggal_lahir'] ?? null);
        $tglBergabung = $this->parseDate($row['tanggal_bergabung'] ?? null);
        $tglResign    = $this->parseDate($row['tanggal_resign'] ?? null);
        $tglPkwt      = $this->parseDate($row['masa_berlaku_pkwt'] ?? null);

        // =========================================================================
        // 2. INSERT / UPDATE STAFF (Berdasarkan NIK)
        // =========================================================================
        $staff = Staff::updateOrCreate(
            ['nik' => $nikBersih],
            [
                'id_staff'                => $row['id_pekerja'] ?? null,
                'nama'                    => $row['nama_lengkap'] ?? null,
                'no_kk'                   => $row['nomor_kk'] ?? null,
                'tempat_lahir'            => $row['tempat_lahir'] ?? null,
                'tgl_lahir'               => $tglLahir,
                'kelamin'                 => $row['jenis_kelamin'] ?? null,
                'pendidikan'              => $row['pendidikan'] ?? null,
                'status_kawin'            => $row['status_perkawinan'] ?? null,
                'anak'                    => $row['jumlah_anak'] ?? 0,
                
                'tgl_bergabung'           => $tglBergabung,
                'tgl_resign'              => $tglResign,
                'status_aktif'            => empty($tglResign) ? 1 : 0,
                
                // Alamat
                'alamat'                  => $row['jalannama_gedung'] ?? null,
                'desa'                    => $row['kelurahandesa'] ?? null,
                'rt'                      => $row['rt'] ?? null,
                'rw'                      => $row['rw'] ?? null,
                'kota'                    => $row['kotakabupaten'] ?? null,
                'kecamatan'               => $row['kecamatan'] ?? null,
                'provinsi'                => $row['provinsi'] ?? null,
                
                // Kontak & BPJS
                'email'                   => $row['email_pribadi'] ?? null,
                'telp'                    => $row['nomor_telepon_pribadi'] ?? null,
                'kpj'                     => $row['bpjs_ketenagakerjaan'] ?? null,
                'naker'                   => $row['bpjs_kesehatan'] ?? null,
                
                // Bank
                'nama_rek'                => $row['nama_bank'] ?? null,
                'rekening'                => $row['no_rekening'] ?? null,
                
                // Pekerjaan
                'perusahaan'              => $row['nama_perusahaan'] ?? null,
                'unit_kerja'              => $row['penempatan_unit'] ?? null,
                'jabatan'                 => $row['jabatan'] ?? null,
                'status_perjanjian_kerja' => $row['status_karyawan'] ?? null,
                'masa_berlaku_pkwt'       => $tglPkwt,
                
                // Emergency
                'nama_emergency'          => $row['nama_kontak_emergency'] ?? null,
                'telp_emergency'          => $row['nomor_telepon_darurat'] ?? null,
                'hubungan_emergency'      => $row['hubungan'] ?? null,
                'ibu_kandung'             => $row['ibu_kandung'] ?? null,
            ]
        );

// =========================================================================
        // 3. LOGIKA PEMBUATAN AKUN (USER)
        // =========================================================================
        $jabatan = $staff->jabatan;

        // Logika fuzzy matching jabatan untuk role dan status_akun
        if (!empty($jabatan) && !empty($staff->email) && !empty($tglLahir)) {
            $rawJabatan = trim($jabatan ?? '');
            $lower = strtolower($rawJabatan);
            $role = !empty($lower) ? $lower : 'staff';
            $statusAkun = 0;

            if (str_contains($lower, 'hrd')) {
                $role = 'hrd';
                $statusAkun = 1;
            } elseif (str_contains($lower, 'pic')) {
                $role = 'pic';
                $statusAkun = 1;
            } elseif (str_contains($lower, 'akuntan') || str_contains($lower, 'akuntansi') || str_contains($lower, 'finance') || str_contains($lower, 'accounting')) {
                $role = 'akuntan';
                $statusAkun = 1;
            } elseif (str_contains($lower, 'admin')) {
                $role = 'admin';
                $statusAkun = 1;
            } elseif (str_contains($lower, 'head') || str_contains($lower, 'supervisor')) {
                $role = 'head_supervisor';
                $statusAkun = 0;
            } else {
                $role = !empty($lower) ? $lower : 'staff';
                $statusAkun = 0;
            }
            
            $existingUser = User::query()->where('email', $staff->email)->first();
            
            if (!$existingUser) {
                $plainPassword = Carbon::parse($tglLahir)->format('d-m-Y');
                $password = Hash::make($plainPassword);

                $user = User::create([
                    'name'        => $staff->nama,
                    'email'       => $staff->email,
                    'password'    => $password,
                    'role'        => $role,
                    'staff_id'    => $staff->id,
                    'status_akun' => $statusAkun,
                ]);

                // Flash session info
                $statusText = $statusAkun == 1 ? 'Aktif (Bisa Login)' : 'Tidak Aktif (Tidak Bisa Login)';
                $info = 'Akun Staff (' . $user->name . ') dibuat! Email: ' . $user->email . ' | Status: ' . $statusText;
                Session::push('akun_info_import', $info); 
            } else {
                $existingUser->update([
                    'role'        => $role,
                    'status_akun' => $statusAkun,
                ]);
            }
        }

        return $staff;
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

            $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $bulanInggris = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

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