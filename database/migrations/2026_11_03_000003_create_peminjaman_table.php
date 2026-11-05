<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bantex_id')->constrained('bantex')->cascadeOnDelete();
            $table->string('nama_peminjam');
            $table->text('keperluan');
            $table->timestamp('tanggal_pinjam');
            $table->timestamp('tanggal_kembali')->nullable();
            $table->timestamps();

            $table->index(['bantex_id', 'tanggal_kembali']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
