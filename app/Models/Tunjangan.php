<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    protected $table = 'tunjangan';

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
