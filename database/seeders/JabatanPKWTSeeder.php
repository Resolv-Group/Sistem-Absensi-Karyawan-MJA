<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanPKWTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatan = [
            'Kebersihan',
            'Driver',
            'Foreman Paket Insect',
            'Foreman Return',
            'SC / Operator Forklift',
            '5R SC',
            'Formulator',
            'Admin Stock Barang Prod',
            'Kebersihan / Insectisida',
            'Operator Forklift',
            'Operator Forklift / Insectisida',
            'Borongan',
            'Maintenance',
            'Quality Control',
            'Gudang / Borongan Label',
            'OP Forklift & Foreman 5R',
            'Bongkar Muat',
            'Stock Keeper / Finished Good',
            'Gudang / Stock Keeper',
            'Operator Forklift / Finished Good',
            'Administrasi',
            'Cleaning Product Return',
            'Admin Timbang',
            'Stock Keeper / Packaging',
            'Checker',
            'Foreman Bongkar Muat',
            'Foreman Packaging',
            'Foreman Borongan Label',
        ];

        foreach ($jabatan as $nama) {
            DB::table('jabatan_pkwt')->insert([
                'nama' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
