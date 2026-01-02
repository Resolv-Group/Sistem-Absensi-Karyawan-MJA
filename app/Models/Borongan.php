<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borongan extends Model
{
    protected $table = 'borongan';

    protected $fillable = [
        'id_unit',
        'harga_unit',
        'harga_pekerja',
        'kategori',
        'nama_item',
        'satuan',
        'status_aktif',
    ];

    public function kategoriRel() {
        return $this->belongsTo(Kategori::class, 'kategori');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
    }
}
