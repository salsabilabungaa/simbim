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
        Schema::create('skripsi_dokumen_komentars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_dokumen_id')->constrained('skripsi_dokumens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('skripsi_dokumen_komentars')->cascadeOnDelete();
            $table->string('peran_pengirim', 20)->default('dosen');
            $table->string('lokasi_dokumen', 150)->default('Umum / Dokumen');
            $table->text('kutipan_teks')->nullable();
            $table->text('isi_komentar');
            $table->string('status', 40)->default('Perlu Revisi');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->index(['skripsi_dokumen_id', 'parent_id'], 'skripsi_komentar_dok_parent_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsi_dokumen_komentars');
    }
};
