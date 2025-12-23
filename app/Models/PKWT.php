<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKWT extends Model
{
    use HasFactory;

    protected $table = 'pkwt_pekerja';

    protected $fillable = [
        'id_pekerja','id_unit','divisi','jabatan',
        'tgl_mulai_pkwt','tgl_akhir_pkwt',
        'dokumen_pkwt','status_aktif', 'gaji_harian'
    ];

}
