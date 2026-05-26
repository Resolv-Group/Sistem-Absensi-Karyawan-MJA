<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi_Borongan extends Model
{
    protected $table = 'absensi_borongan';

    protected $fillable = [
        'id_absensi',
        'id_detil_borongan',
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_absensi', 'id');
    }

    public function detilBorongan()
    {
        return $this->belongsTo(Detil_Borongan::class, 'id_detil_borongan', 'id');
    }
}
