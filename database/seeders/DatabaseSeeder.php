<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(BidangUsahaSeeder::class);
        $this->call(MitraKerjaSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(DivisiSeeder::class);
        $this->call(JabatanPKWTSeeder::class);
        $this->call(KategoriSeeder::class);
        $this->call(ShiftSeeder::class);
        // $this->call(PenilaianSeeder::class);
    }
}
