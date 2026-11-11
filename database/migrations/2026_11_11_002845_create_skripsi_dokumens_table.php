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
        Schema::create('skripsi_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('program_studi', 120)->default('Pendidikan Sistem dan Teknologi Informasi (PSTI)');
            $table->string('peran_dosen', 60)->default('Pembimbing Utama');
            $table->string('judul_skripsi');
            $table->string('bab_aktif', 120)->default('Bab III (Metodologi Penelitian)');
            $table->string('nama_file')->default('Draft_Skripsi_v1.0.pdf');
            $table->string('file_path')->nullable();
            $table->string('versi', 20)->default('v1.0');
            $table->unsignedTinyInteger('progres_persen')->default(25);
            $table->string('status_draf', 60)->default('Menunggu Review Dosen');
            $table->string('badge_status', 20)->default('warning');
            $table->unsignedSmallInteger('jumlah_sesi')->default(1);
            $table->longText('konten_naskah')->nullable();
            $table->text('catatan_akhir_dosen')->nullable();
            $table->timestamps();

            $table->index(['dosen_pembimbing_id', 'status_draf'], 'skripsi_dok_dosen_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsi_dokumens');
    }
};
