<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'id_unit',
        'id_mitra_kerja',
        'mulai_perjanjian',
        'akhir_perjanjian',
        'dokumen_mou',
        'nama_unit',
        'persentase_management_fee',
        'sistem_pengajian',
        'status_aktif'
    ];

    protected $casts = [
        'mulai_perjanjian' => 'date',
        'akhir_perjanjian' => 'date',
    ];

    public function namaMitra()
    {
        return $this->belongsTo(MitraKerja::class, 'id_mitra_kerja');
    }

    public function picUnit()
    {
        return $this->hasMany(PicUnit::class, 'id_unit', 'id');
    }

    // public function pkwtPekerja() {
    //     return $this->hasOne(PKWT::class, 'id_unit', 'id_unit');
    // }

    public function pkwt()
    {
        return $this->hasMany(PKWT::class, 'id_unit', 'id');
    }

    public function pics()
    {
        return $this->belongsToMany(
            User::class,
            'pic_unit',
            'id_unit',
            'id_pic'
        );
    }

    public function borongan()
    {
        // 'id_unit' is the foreign key on the borongan table
        return $this->hasMany(Borongan::class, 'id_unit');
    }

    public function detilHarian()
    {
        return $this->hasMany(Detil_Harian::class, 'unit_id');
    }

    //Accessor
    public function getTotalPekerjaAttribute()
    {
        return $this->pkwt()->count();
    }

    // 2️⃣ TOTAL HADIR
    public function getTotalHadirAttribute()
    {
        $date = request('date') ?? now()->toDateString();

        if ($this->sistem_pengajian == 1) {
            return Detil_Harian::whereHas('absensi', fn ($q) =>
                $q->where('id_unit', $this->id)
                ->whereDate('tgl_absensi', $date)
            )
            ->where('status_kehadiran', 1)
            ->count();
        }

        return Detil_Borongan::whereHas('absensi', fn ($q) =>
            $q->where('id_unit', $this->id)
            ->whereDate('tgl_absensi', $date)
            ->whereNot('status_kehadiran', 2)
        )
        ->distinct('id_absensi')
        ->count('id_absensi');

        // dd($this->id);         

        // dd(
        //     Detil_Borongan::whereHas('absensi', fn ($q) =>
        //         $q->where('id_unit', $this->id)
        //         ->whereDate('tgl_absensi', $date)
        //     )
        //     ->count()
        // );
    }

    // 3️⃣ TOTAL ABSEN
    public function getTotalBelumAbsenAttribute()
    {
        $total = $this->total_pekerja;
        $hadir = $this->total_hadir;

        // dd($total, $hadir);

        return max($total - $hadir, 0);
    }

    // 4️⃣ PEKERJA DEKAT PENILAIAN (PKWT H-7)
    public function getTotalPenilaianAttribute()
    {
        return $this->pkwt()
            ->whereBetween('tgl_akhir_pkwt', [
                Carbon::today(),
                Carbon::today()->addDays(7)
            ])
            ->count();
    }

}
