<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'Absensi';

    protected $fillable = [
        'id_pekerja',
        'id_pic',
        'tgl_absensi',
        'id_unit',
        'tipe',
        'verifikasi'
    ];

    
}
