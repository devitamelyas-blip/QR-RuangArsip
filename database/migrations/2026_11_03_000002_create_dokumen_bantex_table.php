<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_bantex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bantex_id')->constrained('bantex')->cascadeOnDelete();
            $table->string('no_pengadaan')->index();
            $table->string('nama_pengadaan')->index();
            $table->string('tahun', 10)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_bantex');
    }
};
