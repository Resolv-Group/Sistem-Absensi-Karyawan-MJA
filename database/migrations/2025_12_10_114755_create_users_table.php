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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();

            $table->string('password');

            // ROLE USER
            // $table->enum('role', ['pic', 'akuntan', 'hrd', 'admin', 'staff', 'head_supervisor'])->default('pic');
            $table->string('role', 100)->default('staff');

            // RELASI KE STAFF (BOLEH NULL JIKA ADMIN)
            $table->foreignId('staff_id')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();

            $table->unsignedTinyInteger('status_akun')->default(1)->comment('1=can login, 0=cannot login');

            // LARAVEL DEFAULT
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
