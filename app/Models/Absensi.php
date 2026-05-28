<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'id_pekerja',
        'id_pic',
        'tgl_absensi',
        'id_unit',
        'tipe',
        'verifikasi',
    ];

    public function detilHarian()
    {
        return $this->hasOne(Detil_Harian::class, 'id_absensi', 'id');
    }

    public function detilBorongan()
    {
        return $this->hasMany(Detil_Borongan::class, 'id_absensi', 'id');
    }

    public function absensiBorongan()
    {
        return $this->hasMany(Absensi_Borongan::class, 'id_absensi', 'id');
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

    // Absensi model — returns the correct detil regardless of group or individual
    public function getEffectiveDetilBorongan()
    {
        // If this absensi has pivot records, it's a group absen
        // → load detil FROM the pivot's detilBorongan
        if ($this->absensiBorongan->isNotEmpty()) {
            return $this->absensiBorongan
                ->map(fn ($pivot) => $pivot->detilBorongan)
                ->filter()
                ->values();
        }

        // Otherwise individual — detil is directly on this absensi
        return $this->detilBorongan;
    }
}
