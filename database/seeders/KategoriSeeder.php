<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            'Alih Daya',
            'Makanan',
            'Rokok',
            'Gabus Filter & Kertas',
            'Kantor Distributor',
            'Cengkeh',
            'Percetakan',
            'Rumah Ibadah',
            'Gudang Berkas',
            'Sandal',
            'Perikanan & Makanan',
            'Frozen Food',
            'Furniture & Electronic',
            'Packaging',
            'Kabel',
            'Plastik',
            'Plastik Injeksi',
            'Pakan Ternak',
            'Sparepart',
            'Perhiasan Emas',
            'Pintu Platinum',
            'Sarana Pertanian',
            'Bahan Makanan',
            'Kedinasan',
            'Lem Raja Wali',
            'Air Minum Kemasan',
            'Perkayuan',
            'Tekstil',
            'Bibit & Jagung',
            'Pertanian',
            'Supermarket',
            'Toko Bangunan',
            'Perdagangan',
            'Logistik',
            'Olahan Kaca',
        ];

        foreach ($kategori as $nama) {
            DB::table('kategori')->insert([
                'nama' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
