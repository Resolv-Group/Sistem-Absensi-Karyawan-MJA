<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nama = [
            'Rue',
            'Km',
            'Kg',
            'Pieces',
            'M',
            'Cm'
        ];

        foreach ($nama as $namas) {
            DB::table('satuan')->insert([
                'nama' => $namas,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
