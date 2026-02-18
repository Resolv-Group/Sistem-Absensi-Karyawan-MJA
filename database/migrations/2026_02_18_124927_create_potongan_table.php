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
        Schema::create('potongan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pkwt');
            $table->integer('id_unit');

            $table->char('kategori');
            $table->integer('total');
            $table->char('keterangan');

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
        Schema::dropIfExists('potongan');
    }
};
