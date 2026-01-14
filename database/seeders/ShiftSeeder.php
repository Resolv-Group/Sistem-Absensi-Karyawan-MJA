<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shift_absen')->insert([
            [
                'id_unit' => 1,
                'nama' => 'pagi',
                'waktu_masuk'  => '07:00:00',
                'waktu_keluar' => '15:00:00',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_unit' => 1,
                'nama' => 'siang',
                'waktu_masuk'  => '15:00:00',
                'waktu_keluar' => '23:00:00',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_unit' => 1,
                'nama' => 'malam',
                'waktu_masuk'  => '23:00:00',
                'waktu_keluar' => '07:00:00',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
