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
        Schema::table('payroll_history_details', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('email');
            $table->string('jabatan')->nullable()->after('divisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_history_details', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'jabatan']);
        });
    }
};
