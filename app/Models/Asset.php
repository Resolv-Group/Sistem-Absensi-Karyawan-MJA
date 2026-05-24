<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'asset';

    protected $fillable = [
        'id_unit',
        'nama_barang',
        'jumlah',
        'tahun_perolehan',
        'harga_perolehan',
        'lokasi',
    ];
}
