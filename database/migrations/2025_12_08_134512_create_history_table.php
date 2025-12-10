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
        Schema::create('history', function (Blueprint $table) {

            $table->bigIncrements('id_history'); 
            $table->unsignedBigInteger('foreign_id'); 
            $table->string('nama_tabel', 100); 
            $table->string('jabatan', 100); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->timestamp('waktu')->useCurrent(); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
