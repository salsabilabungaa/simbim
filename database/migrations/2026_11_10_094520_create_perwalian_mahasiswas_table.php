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
        Schema::create('perwalian_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dosen_wali_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('program_studi', 120)->index();
            $table->string('angkatan', 10);
            $table->unsignedTinyInteger('semester_berjalan')->default(1);
            $table->string('tahun_akademik', 50)->index();
            $table->decimal('ipk', 3, 2)->default(0.00);
            $table->unsignedSmallInteger('sks_diambil')->default(0);
            $table->string('status_perwalian', 30)->default('belum')->index();
            $table->string('status_krs', 80)->default('Belum Mengisi KRS/IRS');
            $table->date('tanggal_persetujuan_krs')->nullable();
            $table->text('catatan_akhir_wali')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_akademik'], 'perwalian_mhs_tahun_unique');
            $table->index(['tahun_akademik', 'program_studi', 'status_perwalian'], 'perwalian_filter_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perwalian_mahasiswas');
    }
};
