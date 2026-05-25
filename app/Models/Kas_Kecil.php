<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kas_Kecil extends Model
{
    protected $table = 'kas_kecil';

    protected $fillable = [
        'id_unit',
        'akun',
        'tanggal',
        'keterangan',
        'debit',
        'kredit',
        'nota',
        'status',
    ];
}
