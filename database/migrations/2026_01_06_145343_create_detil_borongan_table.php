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

            $table->char('id_absensi')->nullable();
            $table->integer('id_barang');
            $table->integer('status_kehadiran')->default(0);

            $table->float('FD');
            $table->float('act_rej')->nullable();
            $table->float('good_mc')->nullable();
            $table->float('max_rej_subkon')->nullable();
            $table->float('rej_mc_beban')->nullable();

            $table->float('bayaranPerusahaan');
            $table->float('bayaranItem');

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
