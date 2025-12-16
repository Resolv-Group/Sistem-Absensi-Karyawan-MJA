<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangUsaha extends Model
{
    use HasFactory;

    // 1. TELL LARAVEL THE CORRECT TABLE NAME
    protected $table = 'bidang_usaha';

    // 2. DON'T FORGET THIS FROM THE PREVIOUS FIX
    protected $fillable = ['nama'];


}
