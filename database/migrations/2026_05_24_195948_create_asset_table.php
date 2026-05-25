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
        Schema::create('asset', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_unit')->constrained('unit')->cascadeOnDelete();
            $table->string('nama_barang');
            $table->string('keterangan')->nullable();
            $table->integer('jumlah')->default(0);
            $table->date('tahun_perolehan');
            $table->unsignedBigInteger('harga_perolehan')->default(0);
            $table->string('lokasi');

            $table->integer('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
