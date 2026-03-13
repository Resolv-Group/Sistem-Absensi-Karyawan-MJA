<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();

            $table->char('id_unit', 20)->unique();
            $table->char('id_mitra_kerja', 20);

            $table->date('mulai_perjanjian');
            $table->date('akhir_perjanjian');

            // $table->binary('dokumen_mou')->nullable();

            $table->char('nama_unit');
            $table->decimal('persentase_management_fee', 5, 2)->nullable();
            $table->integer('sistem_pengajian');
            $table->float('umk')->nullable();
            $table->decimal('bpjs_kesehatan', 5, 2)->nullable();
            $table->decimal('bpjs_naker', 5, 2)->nullable();
            $table->integer('status_aktif')->default(1);

            $table->json('tunjangan')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE unit ADD dokumen_mou MEDIUMBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
