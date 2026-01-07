<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKWT extends Model
{
    use HasFactory;

    protected $table = 'pkwt_pekerja';

    protected $fillable = ['id_pekerja', 'id_unit', 'divisi_id', 'jabatan_id', 'tgl_mulai_pkwt', 'tgl_akhir_pkwt', 'dokumen_pkwt', 'dokumen_mime', 'status_aktif', 'gaji_harian'];

    public function pekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }

    public function getStatusPkwtAttribute()
    {
        if (!$this->tgl_akhir_pkwt) {
            return [
                'label' => 'Tanpa PKWT',
                'color' => 'gray',
            ];
        }

        $today = Carbon::today();
        $end = Carbon::parse($this->tgl_akhir_pkwt);

        $daysLeft = $today->diffInDays($end, false);

        if ($daysLeft < 0) {
            return [
                'label' => 'Expired',
                'color' => 'red',
            ];
        }

        if ($daysLeft <= 30) {
            return [
                'label' => 'Segera Habis',
                'color' => 'red',
            ];
        }

        return [
            'label' => 'Aktif',
            'color' => 'green',
        ];
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(JabatanPKWT::class);
    }
}
