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
        Schema::create('konsultasi_akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perwalian_mahasiswa_id')->constrained('perwalian_mahasiswas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dosen_wali_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('program_studi', 120)->index();
            $table->string('tahun_akademik', 50)->index();
            $table->date('tanggal_konsultasi')->index();
            $table->string('waktu_konsultasi', 50)->nullable();
            $table->string('metode', 60)->default('Luring (Tatap Muka)');
            $table->string('topik');
            $table->text('catatan_mahasiswa')->nullable();
            $table->text('catatan_dosen_wali');
            $table->string('status_sesi', 40)->default('Selesai')->index();
            $table->string('lampiran_krs')->nullable();
            $table->timestamps();

            $table->index(['program_studi', 'tahun_akademik', 'tanggal_konsultasi'], 'konsultasi_filter_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi_akademiks');
    }
};
