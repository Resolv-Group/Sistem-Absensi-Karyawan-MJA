<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            $table->char('divisi', 100);
            $table->char('jabatan', 100);

            $table->date('tgl_mulai_pkwt');
            $table->date('tgl_akhir_pkwt');

            $table->binary('dokumen_pkwt');

            $table->integer('status_aktif')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkwt_pekerja');
    }
};
