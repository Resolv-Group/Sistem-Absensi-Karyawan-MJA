<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerja extends Model
{
    use HasFactory;

    protected $table = 'pekerja';

    protected $fillable = [
        'nama','nik','no_kk','email','telp','foto','alamat','desa','kecamatan','kota','provinsi',

        'rekening','nama_rek','rt','rw','tempat_lahir','tgl_lahir','tgl_bergabung','tgl_resign',
        
        'kelamin','status_kawin','pendidikan','status_aktif', 'anak',

        'nama_emergency','telp_emergency','hubungan_emergency','ibu_kandung',

    ];
}
