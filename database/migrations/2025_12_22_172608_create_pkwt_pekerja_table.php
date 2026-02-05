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
        Schema::create('pkwt_pekerja', function (Blueprint $table) {
            $table->id();

            $table->char('id_pekerja', 20);
            $table->char('id_unit', 20);

            // $table->char('divisi', 100);
            // $table->char('jabatan', 100);

            $table->integer('divisi_id');
            $table->integer('jabatan_id');

            $table->date('tgl_mulai_pkwt');
            $table->date('tgl_akhir_pkwt');

            $table->string('dokumen_mime', 100)->nullable();

            $table->integer('status_aktif')->default(1);
            $table->unsignedInteger('gaji_harian')->nullable();
            $table->unsignedInteger('gaji_overtime')->nullable();

            $table->integer('bpjs_kesehatan')->default(0);
            $table->integer('bpjs_naker')->default(0);

            $table->timestamps();
        });

        DB::statement('ALTER TABLE pkwt_pekerja ADD dokumen_pkwt MEDIUMBLOB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkwt_pekerja');
    }
};
