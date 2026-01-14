<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift_Absen extends Model
{
    use HasFactory;

    protected $table = 'shift_absen';

    protected $fillable = ['id_unit','nama','waktu_masuk', 'waktu_keluar'];
}
