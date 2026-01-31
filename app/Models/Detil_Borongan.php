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
        'FD',
        'act_rej',
        'good_mc',
        'bayaranPerusahaan',
        'bayaranItem',
        'buktiSuratJalan',
        'catatan',
        'updated_by',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id');
    }

    public function borongan()
    {
        return $this->belongsTo(Borongan::class, 'id_barang');
    }
}
