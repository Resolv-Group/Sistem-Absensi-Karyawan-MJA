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
        'status_aktif',
    ];

    protected $casts = [
        'mulai_perjanjian' => 'date',
        'akhir_perjanjian' => 'date',
    ];


}
