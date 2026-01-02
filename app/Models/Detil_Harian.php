<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detil_Harian extends Model
{
    protected $table = 'detil_harian';

    protected $fillable = [
        'id_absensi',
        'status_kehadiran',
        'waktu_masuk',
        'waktu_keluar',
        'catatan'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id');
    }
}
