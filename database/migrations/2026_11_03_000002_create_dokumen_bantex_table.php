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
        Schema::create('dokumen_bantex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bantex_id')->constrained('bantex')->cascadeOnDelete();
            $table->string('no_pengadaan');
            $table->string('nama_pengadaan');
            $table->string('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_bantex');
    }
};

