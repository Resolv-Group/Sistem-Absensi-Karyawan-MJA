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

    public function detilHarian() {
        return $this->hasOne(Detil_Harian::class, 'id_absensi', 'id');
    }

    public function detilBorongan() {
        return $this->hasMany(Detil_Borongan::class, 'id_absensi', 'id');
    }

    public function pekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function tunjangan()
    {
        return $this->hasOne(Tunjangan::class, 'id_absensi');
    }
    public function potongan()
    {
        return $this->hasOne(Potongan::class, 'id_absensi');
    }

    
}
