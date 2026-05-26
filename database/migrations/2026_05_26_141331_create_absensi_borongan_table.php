<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel kecil (pivot) baru tanpa mengganggu tabel lama
        Schema::create('absensi_borongan', function (Blueprint $table) {
            $table->id();
            
            // Menyimpan ID dari tabel Absensi
            $table->unsignedBigInteger('id_absensi');
            
            // Menyimpan ID dari tabel Detil Borongan
            $table->unsignedBigInteger('id_detil_borongan');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensi_borongan');
    }
};