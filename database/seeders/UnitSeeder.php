<?php

namespace Database\Seeders;

use App\Models\Borongan;
use App\Models\Pekerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Unit;
use App\Models\PicUnit;
use App\Models\PKWT;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================
        // ✅ Unit DUMMY
        // ==================
        $faker = Faker::create('id_ID');

        Unit::create([
                'id_unit' => 1,
                'id_mitra_kerja' => rand(1,10),
                'mulai_perjanjian' => $faker->date(),
                'akhir_perjanjian' => $faker->date(),
                'dokumen_mou' => null, // placeholder BLOB
                'nama_unit' => 'Unit Harian',
                'persentase_management_fee' => 10,
                'sistem_pengajian' => 1,
                //Sistem pengajian, 1 = harian, 2 = borongan
                'umk' => 100000,
                'bpjs_kesehatan' => 1,
                'bpjs_naker' => 2,
            ]);

        Unit::create([
                'id_unit' => 2,
                'id_mitra_kerja' => rand(1,10),
                'mulai_perjanjian' => $faker->date(),
                'akhir_perjanjian' => $faker->date(),
                'dokumen_mou' => null, // placeholder BLOB
                'nama_unit' => 'Unit Borongan',
                'persentase_management_fee' => 10,
                'sistem_pengajian' => 2,
                //Sistem pengajian, 1 = harian, 2 = borongan
                'umk' => 100000,
                'bpjs_kesehatan' => 1,
                'bpjs_naker' => 2,
            ]);

        PicUnit::create([
            'id_unit' => 1,
            'id_pic' => 8,
        ]);

        PicUnit::create([
            'id_unit' => 1,
            'id_pic' => 9,
        ]);

        PicUnit::create([
            'id_unit' => 2,
            'id_pic' => 8,
        ]);

        PicUnit::create([
            'id_unit' => 2,
            'id_pic' => 9,
        ]);

        $pekerjaIds = Pekerja::inRandomOrder()->limit(6)->pluck('id')->toArray();

        $units = [1, 2]; // Unit Harian & Unit Borongan
        $index = 0;

        foreach ($units as $unitId) {

            for ($i = 0; $i < 3; $i++) {

                $tglMulai = $faker->dateTimeBetween('-6 months', 'now');
                $tglAkhir = (clone $tglMulai)->modify('+1 year');

                PKWT::create([
                    'id_pekerja'      => $pekerjaIds[$index],
                    'id_unit'         => $unitId,
                    'divisi_id'       => rand(1, 5),
                    'jabatan_id'      => rand(1, 5),
                    'tgl_mulai_pkwt'  => $tglMulai->format('Y-m-d'),
                    'tgl_akhir_pkwt'  => $tglAkhir->format('Y-m-d'),
                    'dokumen_pkwt'    => 'dummy_pkwt.pdf',
                    'dokumen_mime'    => null,
                    'status_aktif'    => 1,
                    'gaji_harian'     => $unitId == 6
                        ? $faker->numberBetween(100000, 150000) // harian
                        : $faker->numberBetween(150000, 250000), // borongan
                    
                    'gaji_overtime' => 100000,
                    'bpjs_kesehatan' => 0,
                    'bpjs_naker' => 0,
                ]);

                $index++;
            }
        }

        $kategoriList = ['Potong', 'Jahit', 'Finishing'];
        $satuanList   = ['PCS', 'LUSIN', 'KODI'];

        for ($i = 1; $i <= 3; $i++) {
            Borongan::create([
                'id_unit'        => 7,
                'nama_item'      => 'Borongan ' . $kategoriList[$i - 1],
                'kategori'       => rand(1, 3),
                'satuan'         => rand(1, 5),
                'Max Rej Subkon' => 1,
                'harga_unit'     => $faker->numberBetween(5000, 15000),
                'harga_pekerja'  => $faker->numberBetween(3000, 10000),
                'status_aktif'   => 1,
            ]);
        }

    }
}
