<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicUnit extends Model
{
    use HasFactory;

    protected $table = 'pic_unit';

    protected $fillable = [
        'id_unit',
        'id_pic',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'id_pic', 'id');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'id_pic', 'id');
    }

}
