<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'id_unit',
        'id_mitra_kerja',
        'mulai_perjanjian',
        'akhir_perjanjian',
        'dokumen_mou',
        'nama_unit',
        'persentase_management_fee',
        'sistem_pengajian',
        'status_aktif'
    ];

    protected $casts = [
        'mulai_perjanjian' => 'date',
        'akhir_perjanjian' => 'date',
    ];

    public function namaMitra()
    {
        return $this->belongsTo(MitraKerja::class, 'id_mitra_kerja');
    }

    public function picUnit()
    {
        return $this->hasMany(PicUnit::class, 'id_unit', 'id');
    }

    public function pkwtPekerja() {
        return $this->hasOne(PKWT::class, 'id_unit', 'id_unit');
    }

    public function pkwt()
    {
        return $this->hasMany(PKWT::class, 'id_unit', 'id');
    }



}
