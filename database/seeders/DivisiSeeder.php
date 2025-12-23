<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisi = [
            'GA',
            'Insectisida',
            'Insectisida / Borongan',
            'Maintenance',
            'Quality Control',
            'Warehouse / Borongan',
            'Warehouse / Finished Good',
            'Warehouse / Packaging',
        ];

        foreach ($divisi as $nama) {
            DB::table('divisi')->insert([
                'nama' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
