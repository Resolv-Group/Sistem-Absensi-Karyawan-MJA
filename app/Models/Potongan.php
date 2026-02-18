<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    protected $table = 'potongan';

    protected $fillable = [
        'id_pkwt',
        'id_unit',
        'id_absensi',
        'kategori',
        'total',
        'keterangan',
        'updated_by',
        'created_by'
    ];
}
