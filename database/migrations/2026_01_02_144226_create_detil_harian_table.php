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
        Schema::create('detil_harian', function (Blueprint $table) {
            $table->id();

            $table->integer('id_absensi');

            $table->decimal('jam_kerja_normal', 4, 1);
            $table->decimal('jam_kerja_harian', 4, 1);
            $table->decimal('overtime', 4, 1)->default(0);
            $table->integer('hbn')->default(0);
            $table->integer('status_kehadiran')->default(0);
            $table->integer('isPaid')->default(0);
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
        Schema::dropIfExists('detil_harian');
    }
};
