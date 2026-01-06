<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detil_Borongan extends Model
{
    protected $table = 'detil_borongan';

    protected $fillable = [
        'id_absensi',
        'id_barang',
        'status_kehadiran',
        'act.rej',
        'rej.mc',
        'bayaranItem',
        'FD',
        'buktiSuratJalan',
        'catatan',
        'updated_by',
    ];
}
