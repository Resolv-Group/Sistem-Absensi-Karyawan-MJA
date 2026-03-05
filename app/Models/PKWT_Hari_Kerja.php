<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PKWT_Hari_Kerja extends Model
{
    protected $table = 'pkwt_hari_kerja';

    protected $fillable = [
        'pkwt_id',
        'hari',
        'jam_kerja',
    ];

    public function pkwt()
    {
        return $this->belongsTo(PKWT::class, 'pkwt_id');
    }

    public function getJamKerjaAttribute($value)
    {
        // Jika nilainya 0, kembalikan string "0"
        if ($value == 0) {
            return "0";
        }
        // Jika ada nilainya, kembalikan seperti biasa
        return $value;
    }
}
