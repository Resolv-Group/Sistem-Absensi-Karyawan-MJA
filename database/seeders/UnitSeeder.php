<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Unit;
use App\Models\PicUnit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================
        // ✅ MITRA KERJA DUMMY
        // ==================
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 5; $i++) {

            Unit::create([
                'id_unit' => $i,
                'id_mitra_kerja' => rand(1,10),
                'mulai_perjanjian' => $faker->date(),
                'akhir_perjanjian' => $faker->date(),
                'dokumen_mou' => null, // placeholder BLOB
                'nama_unit' => 1,
                'persentase_management_fee' => 10,
                'sistem_pengajian' => 1,
                //Sistem pengajian, 1 = harian, 2 = borongan
            ]);
        }

        PicUnit::create([
            'id_unit' => 1,
            'id_pic' => 8,
        ]);

        PicUnit::create([
            'id_unit' => 1,
            'id_pic' => 9,
        ]);
        
    }
}
