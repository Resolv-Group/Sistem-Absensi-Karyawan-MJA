<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';
    protected $primaryKey = 'id_history';
    public $timestamps = false; // karena kita pakai kolom 'when'

    protected $fillable = [
        'foreign_id',
        'nama_tabel',
        'jabatan',
        'updated_by',
        'waktu'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}
