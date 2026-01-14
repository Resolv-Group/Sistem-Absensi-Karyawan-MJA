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
        Schema::create('shift_absen', function (Blueprint $table) {
            $table->id();

            $table->integer('id_unit');
            $table->char('nama');
            $table->time('waktu_masuk')->default('00:00:00');
            $table->time('waktu_keluar')->default('00:00:00');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_absen');
    }
};
