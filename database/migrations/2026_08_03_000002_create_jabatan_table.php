<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->unique();
            $table->timestamps();
        });

        // Seed standard positions
        $now = now();
        DB::table('jabatan')->insertOrIgnore([
            ['nama' => 'HRD', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'PIC', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Akuntansi', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Akuntan', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Admin', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Head Supervisor', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Staff', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Komisaris', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
