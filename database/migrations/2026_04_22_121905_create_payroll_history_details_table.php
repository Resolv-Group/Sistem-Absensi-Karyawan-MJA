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
        Schema::create('payroll_history_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_history_id')->constrained()->cascadeOnDelete();
            
            $table->integer('id_pekerja');
            $table->string('nama');
            $table->string('email')->nullable();

            // SNAPSHOT VALUES
            $table->decimal('upah_pokok', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->decimal('lembur_hbn', 15, 2)->default(0);
            $table->decimal('insentif', 15, 2)->default(0);
            $table->decimal('tunjangan', 15, 2)->default(0);

            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('take_home_pay', 15, 2)->default(0);

            // FILE & EMAIL TRACKING
            $table->string('pdf_path')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->string('email_status')->default('pending'); // pending, sent, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_history_details');
    }
};
