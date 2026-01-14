<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detil_Harian extends Model
{
    protected $table = 'detil_harian';

    protected $fillable = [
        'id_absensi',
        'id_shift',
        'status_kehadiran',
        'waktu_masuk',
        'waktu_keluar',
        'catatan',
        'updated_by'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id');
    }

    public function shiftAbsen()
    {
        // Parameter 1: Model tujuan
        // Parameter 2: Foreign key di tabel detil_harian
        return $this->belongsTo(Shift_Absen::class, 'id_shift');
    }
}
