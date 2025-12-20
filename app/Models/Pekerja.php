<?php

namespace App\Models;

use App\JenisKelamin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerja extends Model
{
    use HasFactory;

    protected $table = 'pekerja';

    //enumerator preload
    protected $casts = [
        'kelamin' => JenisKelamin::class,
    ];

    protected $fillable = [
        'nama','id_pekerja','nik','no_kk','email','telp','foto', 'dokumen' ,'alamat','desa','kecamatan','kota','provinsi',

        'rekening','nama_rek','rt','rw','tempat_lahir','tgl_lahir','tgl_bergabung','tgl_resign','kpj',

        'kelamin','status_kawin','pendidikan','status_aktif', 'anak',

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
