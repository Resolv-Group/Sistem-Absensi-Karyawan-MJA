<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraKerja extends Model
{
    protected $table = 'mitra_kerja';

    protected $fillable = [
        'nama_mitra',
        'bidang_usaha_id',
        'pimpinan',
        'telp_perusahaan',
        'status_pajak',
        'alamat',
        'tgl_mulai_kerjasama',
        'tgl_akhir_mou',
        'status_mou',
        'status_aktif',
        'logo'
    ];
}
