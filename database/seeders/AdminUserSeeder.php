<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pekerja;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'staff_id' => null
            ]
        );

        // ==================
        // ✅ STAFF DUMMY
        // ==================
        for ($i = 1; $i <= 5; $i++) {

            $staff = Staff::create([
                'nama' => $faker->name,
                'nik' => $faker->unique()->numerify('################'),
                'no_kk' => $faker->numerify('################'),
                'tempat_lahir' => $faker->city,
                'tgl_lahir' => $faker->date(),
                'kelamin' => $faker->randomElement([0,1]),
                'pendidikan' => 'S1',
                'status_kawin' => $faker->randomElement(['Belum Kawin', 'Kawin']),
                'anak' => $faker->numberBetween(0,4),
                'tgl_bergabung' => now(),
                'alamat' => $faker->address,
                'desa' => $faker->word,
                'rt' => rand(1,10),
                'rw' => rand(1,10),
                'kecamatan' => $faker->citySuffix,
                'kota' => $faker->city,
                'provinsi' => $faker->state,
                'email' => $faker->unique()->safeEmail,
                'telp' => $faker->phoneNumber,
                'rekening' => $faker->bankAccountNumber,
                'nama_rek' => $faker->name,
                'nama_emergency' => $faker->name,
                'ibu_kandung' => $faker->name,
                'tgl_resign' => $faker->date(),
                'masa_berlaku_pkwt' => $faker->date(),

                'hubungan_emergency' => $faker->randomElement(['Orang Tua', 'Pasangan', 'Wali']),
                'telp_emergency' => $faker->phoneNumber,
                'jabatan' => $faker->randomElement(['PIC','HRD','Akuntan']),
                'unit_kerja' => 'Head Office',
                'status_perjanjian_kerja' => 'Tetap',
                'status_aktif' => 1,
                'foto' => null
            ]);

            // ==================
            // ✅ AUTOMATIC USER ACCOUNT
            // ==================
            User::create([
                'name' => $staff->nama,
                'email' => $staff->email,
                'password' => Hash::make(Carbon::parse($staff->tgl_lahir)->format('d-m-Y')),
                'role' => strtolower($staff->jabatan),
                'staff_id' => $staff->id
            ]);
        }

        // ==================
        // ✅ PEKERJA DUMMY
        // ==================
        for ($i = 1; $i <= 10; $i++) {

            Pekerja::create([
                'nama' => $faker->name,
                'nik' => $faker->unique()->numerify('################'),
                'no_kk' => $faker->numerify('################'),
                'tempat_lahir' => $faker->city,
                'tgl_lahir' => $faker->date(),
                'kelamin' => $faker->randomElement([0,1]),
                'pendidikan' => 'SMA',
                'status_kawin' => $faker->randomElement(['Belum Kawin', 'Kawin']),
                'anak' => $faker->numberBetween(0,3),
                'tgl_bergabung' => now(),
                'alamat' => $faker->address,
                'desa' => $faker->word,
                'rekening' => $faker->bankAccountNumber,
                'nama_rek' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'rt' => rand(1,10),
                'rw' => rand(1,10),
                'kecamatan' => $faker->citySuffix,
                'kota' => $faker->city,
                'provinsi' => $faker->state,
                'email' => $faker->unique()->safeEmail,
                'telp' => $faker->phoneNumber,
                'rekening' => $faker->bankAccountNumber,
                'nama_rek' => $faker->name,
                'nama_emergency' => $faker->name,
                'ibu_kandung' => $faker->name,
                'tgl_resign' => $faker->date(),

                'hubungan_emergency' => $faker->randomElement(['Orang Tua', 'Pasangan', 'Wali']),
                'telp_emergency' => $faker->phoneNumber,
                'status_aktif' => 1,
                'foto' => null,
            ]);
        }
    }
}
