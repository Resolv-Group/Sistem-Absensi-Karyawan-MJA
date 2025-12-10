<?php

namespace App\Models;

use App\JenisKelamin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    //enumerator preload
    protected $casts = [
        'kelamin' => JenisKelamin::class,
    ];


    protected $fillable = [
        'nama','nik','no_kk','email','telp','foto','alamat','desa','kecamatan','kota','provinsi',

        'unit_kerja','rekening','nama_rek','rt','rw','tempat_lahir','tgl_lahir','tgl_bergabung','tgl_resign',

        'masa_berlaku_pkwt', 

        'kelamin','status_kawin','pendidikan','status_aktif',
        
        'status_perjanjian_kerja', 'jabatan', 'anak',

        'nama_emergency','telp_emergency','hubungan_emergency','ibu_kandung',
    ];

    protected $hidden = [
        'foto',
    ];

    public function getImageBase64Attribute()
    {
        // Check if the 'foto' column has data
        if ($this->foto) {
            // Convert binary data to a Base64 string that HTML can read
            return 'data:image/jpeg;base64,' . base64_encode($this->foto);
        }

        // Return null if no photo exists
        return null;
    }
}
