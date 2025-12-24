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
        Schema::create('borongan', function (Blueprint $table) {
            $table->id();

            $table->double('harga_unit');
            $table->double('harga_pekerja');
            $table->char('id_unit', 20);

            $table->integer('kategori');
            $table->char('nama_item');
            $table->integer('satuan');

            $table->boolean('status_aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borongan');
    }
};
