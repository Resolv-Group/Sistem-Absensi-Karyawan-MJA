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

}
