<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bantex', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bantex')->unique();
            $table->string('slug')->unique();
            $table->string('tim');
            $table->string('nomor_rak');
            $table->string('baris');
            $table->string('urutan_bantex');
            $table->text('keterangan_lokasi')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bantex');
    }
};
