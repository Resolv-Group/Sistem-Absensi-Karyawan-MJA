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
        Schema::create('kas_kecil', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_unit')->constrained('unit')->cascadeOnDelete();
            $table->string('akun');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('debit')->default(0);
            $table->unsignedInteger('kredit')->default(0);

            $table->integer('status')->default(1);

            $table->timestamps();
        });

        DB::statement('ALTER TABLE kas_kecil ADD nota MEDIUMBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_kecil');
    }
};
