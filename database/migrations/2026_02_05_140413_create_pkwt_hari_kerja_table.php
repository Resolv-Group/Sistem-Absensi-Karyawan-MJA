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
        Schema::create('pkwt_hari_kerja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pkwt_id')->constrained('pkwt_pekerja')->onDelete('cascade');

            // Nama hari sebagai ENUM
            $table->enum('hari', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']);

            // Jam kerja sebagai Decimal
            $table->decimal('jam_kerja', 4, 1)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkwt_hari_kerja');
    }
};
