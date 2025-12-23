<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanPKWT extends Model
{
    use HasFactory;

    protected $table = 'jabatan_pkwt';

    protected $fillable = ['nama'];
}
