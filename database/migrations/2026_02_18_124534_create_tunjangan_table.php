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
        Schema::create('tunjangan', function (Blueprint $table) {
            $table->id();

            $table->integer('id_pekerja');
            $table->integer('id_unit');
            $table->integer('id_absensi');

            $table->json('kategori');
            $table->integer('total');
            $table->char('keterangan')->nullable();

            $table->integer(column: 'updated_by');
            $table->integer(column: 'created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangan');
    }
};
