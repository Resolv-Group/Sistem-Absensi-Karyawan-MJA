<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            $table->string('nama', 150);
            $table->string('nik', 30)->unique();
            $table->string('no_kk', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telp', 30)->nullable();
            $table->binary('foto')->nullable();

            $table->string('alamat', 255);
            $table->string('desa', 100);
            $table->string('kecamatan', 100);
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->string('unit_kerja', 100);
            $table->string('rekening', 50)->nullable();
            $table->string('nama_rek', 50)->nullable();

            $table->unsignedTinyInteger('rt')->nullable();
            $table->unsignedTinyInteger('rw')->nullable();

            $table->string('tempat_lahir', 100);
            $table->date('tgl_lahir');
            $table->date('tgl_bergabung');
            $table->date('tgl_resign')->nullable();
            $table->date('masa_berlaku_pkwt')->nullable();
            $table->string('perusahaan', 100);

            $table->boolean('kelamin')->comment('1=laki,0=perempuan');
            $table->string('status_kawin', 50);
            $table->string('pendidikan', 50);

            $table->boolean('status_aktif')->default(1);
            $table->string('status_perjanjian_kerja');
            $table->string('jabatan');

            $table->unsignedTinyInteger('anak')->default(0);

            $table->string('nama_emergency', 150);
            $table->string('hubungan_emergency', 50);
            $table->string('telp_emergency', 30);

            $table->string('ibu_kandung', 150);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('staff');
    }
};


