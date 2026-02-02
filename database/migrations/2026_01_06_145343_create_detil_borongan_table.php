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

            $table->integer('FD');
            $table->integer('act_rej');
            $table->integer('good_mc');
            $table->integer('max_rej_subkon');
            $table->integer('rej_mc_beban');

            $table->integer('bayaranPerusahaan');
            $table->integer('bayaranItem');

            $table->binary('buktiSuratJalan')->nullable();

            $table->char('catatan')->nullable();

            $table->integer('updated_by');

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
