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
            $table->enum('role', ['pic', 'akuntan', 'hrd', 'admin'])->default('pic');

            // RELASI KE STAFF (BOLEH NULL JIKA ADMIN)
            $table->foreignId('staff_id')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();

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
