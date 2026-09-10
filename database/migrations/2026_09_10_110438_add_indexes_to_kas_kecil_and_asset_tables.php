<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds composite indexes to speed up the detail-unit page queries:
     * - kas_kecil: WHERE id_unit = ? AND status IN (1,2) ORDER BY tanggal DESC
     * - asset:     WHERE id_unit = ? AND status IN (1,2) ORDER BY tahun_perolehan DESC
     */
    public function up(): void
    {
        Schema::table('kas_kecil', function (Blueprint $table) {
            $table->index(['id_unit', 'status', 'tanggal'], 'kas_kecil_unit_status_tanggal_idx');
        });

        Schema::table('asset', function (Blueprint $table) {
            $table->index(['id_unit', 'status', 'tahun_perolehan'], 'asset_unit_status_perolehan_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_kecil', function (Blueprint $table) {
            $table->dropIndex('kas_kecil_unit_status_tanggal_idx');
        });

        Schema::table('asset', function (Blueprint $table) {
            $table->dropIndex('asset_unit_status_perolehan_idx');
        });
    }
};
