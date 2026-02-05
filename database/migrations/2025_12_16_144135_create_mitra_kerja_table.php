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
        Schema::create('mitra_kerja', function (Blueprint $table) {
            $table->id(); // ID

            $table->string('nama_mitra');          // Nama Mitra
            $table->string('pimpinan');            // Pimpinan
            $table->string('alamat');              // Alamat
            $table->string('kota');

            $table->integer('bidang_usaha_id');       // Bidang Usaha (bisa FK nanti)

            $table->string('telp_perusahaan', 20); // Telp Perusahaan

            $table->boolean('status_aktif')->default(1); // Status Aktif
            $table->string('status_mou');          // Status MoU
            $table->string('status_pajak');        // Status Pajak

            $table->date('tgl_mulai_kerjasama');   // Tgl Mulai Kerjasama
            $table->date('tgl_akhir_mou')->nullable(); // Tgl Akhir MoU

            $table->timestamps();
        });

        DB::statement('ALTER TABLE mitra_kerja ADD foto MEDIUMBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_kerja');
    }
};
