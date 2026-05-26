<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPKWT extends Model
{
    protected $table = 'history_pkwt';

    protected $fillable = [
        'id_pekerja',
        'id_pkwt',
        'tgl_awal',
        'tgl_akhir',
        'dokumen_pkwt',
    ];
    
}
