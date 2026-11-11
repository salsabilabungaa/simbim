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
        Schema::create('skripsi_diskusi_pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_dokumen_id')->constrained('skripsi_dokumens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referensi_komentar_id')->nullable()->constrained('skripsi_dokumen_komentars')->nullOnDelete();
            $table->string('peran_pengirim', 20)->default('dosen');
            $table->text('pesan');
            $table->timestamps();

            $table->index(['skripsi_dokumen_id', 'created_at'], 'skripsi_diskusi_dok_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsi_diskusi_pesans');
    }
};
