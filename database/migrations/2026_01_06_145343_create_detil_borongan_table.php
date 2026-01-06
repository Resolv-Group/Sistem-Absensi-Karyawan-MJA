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
        Schema::create('detil_borongan', function (Blueprint $table) {
            $table->id();

            $table->char('id_absensi');
            $table->integer('id_barang');
            $table->integer('status_kehadiran')->default(0);
            
            $table->integer('act.rej');
            $table->integer('rej.mc');
            $table->integer('totalQTY');
            $table->integer('bayaranItem');
            $table->integer('FD');

            $table->binary('buktiSuratJalan');

            $table->char('catatan');

            $table->int('updated_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detil_borongan');
    }
};
