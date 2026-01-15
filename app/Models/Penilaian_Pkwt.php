<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian_Pkwt extends Model
{
    protected $table = 'penilaian_pkwt';

    protected $fillable = [
        'id_unit',
        'id_pekerja',

        'mk',
        'absensi',
        'pengetahuan',
        'kualitas',
        'sikap',
        'total',

        'status_staff',
        'status_hrd',
        'status_aktif',
        'keterangan',

        'updated_by',
        'created_by',
    ];

}
