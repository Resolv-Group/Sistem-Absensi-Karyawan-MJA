<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penilaian_pkwt')->insert([
            [
                'id_pekerja'      => 1,
                'id_unit'         => 6,
                'divisi'  => QA,
                'absensi' => 80,
                'pengetahuan' => 50,
                'kualitas' => 55,
                'sikap' => 35,
                'total' => 56,

                'status_staff' => 0,
                'status_hrd' => 0,
                'status_aktif' => 1,

                'keterangan' => 'Kerja',

                'updated_by' => 6,
                'created_by' => 6,
            ],
            [
                'id_pekerja'      => 2,
                'id_unit'         => 6,
                'divisi'  => QA,
                'absensi' => 80,
                'pengetahuan' => 50,
                'kualitas' => 55,
                'sikap' => 35,
                'total' => 56,

                'status_staff' => 0,
                'status_hrd' => 0,
                'status_aktif' => 1,

                'keterangan' => 'Kerja',

                'updated_by' => 6,
                'created_by' => 6,
            ],
            [
                'id_pekerja'      => 3,
                'id_unit'         => 6,
                'mk'  => 1,
                'absensi' => 80,
                'pengetahuan' => 50,
                'kualitas' => 55,
                'sikap' => 35,
                'total' => 56,

                'status_staff' => 0,
                'status_hrd' => 0,
                'status_aktif' => 1,

                'keterangan' => 'Kerja',

                'updated_by' => 6,
                'created_by' => 6,
            ],
        ]);
    }
}
