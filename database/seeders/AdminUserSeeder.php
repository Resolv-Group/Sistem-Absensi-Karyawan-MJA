<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pekerja;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // ==================
        // ✅ STAFF DUMMY + USER OTOMATIS
        // ==================
        for ($i = 1; $i <= 5; $i++) {

            $staff = Staff::create([
                'nama' => $faker->name,
                'id_staff' => $faker->unique()->numerify('################'),
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

                'kpj' => $faker->unique()->numerify('#############'),
                'nama_emergency' => $faker->name,
                'ibu_kandung' => $faker->name,
                'tgl_resign' => $faker->date(),
                'masa_berlaku_pkwt' => $faker->date(),
                'perusahaan' => $faker->name,
                'hubungan_emergency' => 'Orang Tua',
                'telp_emergency' => $faker->phoneNumber,
                'jabatan' => $faker->randomElement(['PIC','HRD','Akuntan']),
                'unit_kerja' => 'Head Office',
                'status_perjanjian_kerja' => 'Tetap',
                'status_aktif' => 1,
                'foto' => null
            ]);

            // ✅ USER UNTUK STAFF
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
        for ($i = 1; $i <= 15; $i++) {

            Pekerja::create([
                'nama' => $faker->name,
                'id_pekerja' => $faker->unique()->numerify('################'),
                'nik' => $faker->unique()->numerify('################'),
                'no_kk' => $faker->numerify('################'),
                'tempat_lahir' => $faker->city,
                'tgl_lahir' => $faker->date(),
                'kelamin' => $faker->randomElement([0,1]),
                'pendidikan' => $faker->randomElement(['SMA','D3','S1']),
                'status_kawin' => $faker->randomElement(['Belum Kawin','Kawin']),
                'anak' => $faker->numberBetween(0,4),
                'tgl_bergabung' => $faker->dateTimeBetween('-2 years','now'),
                'tgl_resign' => $faker->date(),

                // ALAMAT
                'alamat' => $faker->address,
                'desa' => $faker->word,
                'rt' => rand(1,10),
                'rw' => rand(1,10),
                'kecamatan' => $faker->citySuffix,
                'kota' => $faker->city,
                'provinsi' => $faker->state,

                // KONTAK
                'email' => $faker->safeEmail,
                'telp' => $faker->phoneNumber,
                'kpj' => $faker->unique()->numerify('#############'),

                // BANK
                'rekening' => $faker->bankAccountNumber,
                'nama_rek' => $faker->name,

                // DARURAT
                'nama_emergency' => $faker->name,
                'hubungan_emergency' => $faker->randomElement(['Orang Tua','Pasangan','Saudara']),
                'telp_emergency' => $faker->phoneNumber,
                'ibu_kandung' => $faker->name,

                // SYSTEM
                'status_aktif' => 1,
                'foto' => null
            ]);
        }

        // ==================
        // ✅ STAFF ROLE LOGIN DEBUG
        // ==================
        $roles = [
            ['jabatan' => 'admin', 'email' => 'admin@company.com', 'password' => 'admin123'],
            ['jabatan' => 'HRD', 'email' => 'hrd@gmail.com', 'password' => 'hrd123'],
            ['jabatan' => 'PIC', 'email' => 'pic@gmail.com', 'password' => 'pic123'],
            ['jabatan' => 'Akuntan', 'email' => 'akuntan@gmail.com', 'password' => 'akuntan123'],
        ];

        foreach ($roles as $role) {

            $staff = Staff::create([
                'nama' => $role['jabatan'].' System',
                'id_staff' => $faker->unique()->numerify('################'),
                'nik' => $faker->unique()->numerify('################'),
                'no_kk' => $faker->numerify('################'),
                'tempat_lahir' => $faker->city,
                'tgl_lahir' => '2000-01-01',
                'kelamin' => 1,
                'pendidikan' => 'S1',
                'status_kawin' => 'Belum Kawin',
                'anak' => 0,
                'tgl_bergabung' => now(),
                'alamat' => 'Kantor Pusat',
                'desa' => 'Admin',
                'rt' => 1,
                'rw' => 1,
                'kecamatan' => 'Central',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'email' => $role['email'],
                'telp' => '08123456789',
                'rekening' => $faker->bankAccountNumber,
                'nama_rek' => $role['jabatan'],
                'nama_emergency' => 'Emergency',
                'ibu_kandung' => $faker->name,
                'perusahaan' => $faker->name,
                'tgl_resign' => null,
                'masa_berlaku_pkwt' => null,
                'kpj' => $faker->unique()->numerify('#############'),
                'hubungan_emergency' => 'Orang Tua',
                'telp_emergency' => '08111111111',
                'jabatan' => $role['jabatan'],
                'unit_kerja' => 'Head Office',
                'status_perjanjian_kerja' => 'Tetap',
                'status_aktif' => 1,
                'foto' => null
            ]);

            User::firstOrCreate(
                ['email' => $role['email']],
                [
                    'name' => $staff->nama,
                    'password' => Hash::make($role['password']),
                    'role' => strtolower($role['jabatan']),
                    'staff_id' => $staff->id
                ]
            );
        }

    }
}
