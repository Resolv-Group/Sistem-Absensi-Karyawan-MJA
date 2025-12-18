<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\MitraKerja;

class MitraKerjaSeeder extends Seeder
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

        for ($i = 1; $i <= 15; $i++) {

            MitraKerja::create([
                'nama_mitra' => $faker->name,
                'bidang_usaha_id' => $faker->randomElement([1,15]),
                'pimpinan' => $faker->name,
                'telp_perusahaan'=> $faker->phoneNumber,
                'status_pajak'=> $faker->randomElement(['PKP','Non-PKP']),
                'alamat' => $faker->address,
                'tgl_mulai_kerjasama' => $faker->date(),
                'tgl_akhir_mou' => $faker->date(),
                'status_mou' => $faker->randomElement(array: ['Aktif Disnaker','Perpanjangan', 'Tidak Aktif']),
                'status_aktif' => 1,
                'foto' => null,
            ]);
        }
    }
}
