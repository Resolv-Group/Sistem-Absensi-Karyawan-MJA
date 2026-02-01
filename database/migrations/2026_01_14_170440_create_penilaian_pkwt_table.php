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
        Schema::create('penilaian_pkwt', function (Blueprint $table) {
            $table->id();

            $table->integer('id_pekerja');
            $table->integer('id_unit');

            $table->integer('mk');
            $table->integer('absensi');
            $table->integer('pengetahuan');
            $table->integer('kualitas');
            $table->integer('sikap');
            $table->integer('total');

            $table->integer('status_staff');
            $table->integer('status_hrd');
            $table->integer('status_aktif');

            $table->char('keterangan')->nullable();

            $table->integer('updated_by');
            $table->integer('created_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_pkwt');
    }
};
