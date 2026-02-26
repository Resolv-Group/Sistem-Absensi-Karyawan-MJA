<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    protected $table = 'tunjangan';

    protected $fillable = [
        'id_pekerja',
        'id_unit',
        'id_absensi',
        'kategori',
        'total',
        'keterangan',
        'updated_by',
        'created_by'
    ];

    protected $casts = [
        'kategori' => 'json', // Cast kolom kategori sebagai array (JSON)
    ];

    public function absensi() {
        return $this->belongsTo(Absensi::class, 'id_absensi');
    }

    
}
