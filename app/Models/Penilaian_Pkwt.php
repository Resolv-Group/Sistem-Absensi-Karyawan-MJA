<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian_Pkwt extends Model
{
    protected $table = 'penilaian_pkwt';

    protected $fillable = [
        'id_unit',
        'id_pekerja',

        'mk',
        'absensi',
        'pengetahuan',
        'kualitas',
        'sikap',
        'total',

        'status_staff',
        'status_hrd',
        'status_aktif',
        'keterangan',

        'updated_by',
        'created_by',
    ];

    public function pekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    //buat view penilaian (more than 1 item)
    public function pkwt()
    {
        // Menghubungkan kembali ke PKWT berdasarkan id_pekerja
        return $this->belongsTo(PKWT::class, 'id_pekerja', 'id_pekerja')
                    ->whereColumn('id_unit', 'penilaian_pkwt.id_unit');
    }
}
