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
        Schema::create('skripsis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('bidang_minat')->nullable();
            $table->string('tahap')->default('Penyusunan Proposal'); // Penyusunan Proposal, Proses Bimbingan Aktif, Siap Sidang, Revisi Final, Lulus/Selesai

            // Plotting Dosen Pembimbing & Penguji
            $table->foreignId('pembimbing_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pembimbing_1_nama')->nullable();
            $table->foreignId('pembimbing_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pembimbing_2_nama')->nullable();
            $table->foreignId('penguji_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('penguji_1_nama')->nullable();
            $table->foreignId('penguji_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('penguji_2_nama')->nullable();

            // Validasi Pendaftaran Sidang (Approval)
            $table->unsignedInteger('total_bimbingan')->default(0);
            $table->boolean('acc_pembimbing')->default(false);
            $table->text('catatan_pembimbing')->nullable();
            $table->string('file_syarat_sidang')->nullable();
            $table->string('status_pendaftaran_sidang')->default('Belum Mendaftar'); // Belum Mendaftar, Menunggu Persetujuan, Disetujui, Ditolak
            $table->text('catatan_validasi_sidang')->nullable();
            $table->dateTime('tanggal_validasi_sidang')->nullable();

            // Jadwal Pelaksanaan Sidang
            $table->string('jenis_sidang')->default('Sidang Skripsi / Munaqasyah');
            $table->dateTime('jadwal_sidang')->nullable();
            $table->string('ruangan_sidang')->nullable();
            $table->string('metode_sidang')->default('Offline / Luring');
            $table->string('status_sidang')->default('Belum Sidang'); // Belum Sidang, Dijadwalkan, Selesai

            // Verifikasi Berkas Pasca-Sidang & Kelulusan
            $table->string('file_revisi_final')->nullable();
            $table->text('catatan_revisi_final')->nullable();
            $table->dateTime('tanggal_unggah_revisi')->nullable();
            $table->decimal('nilai_pembimbing_1', 5, 2)->nullable();
            $table->decimal('nilai_pembimbing_2', 5, 2)->nullable();
            $table->decimal('nilai_penguji_1', 5, 2)->nullable();
            $table->decimal('nilai_penguji_2', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('nilai_huruf', 5)->nullable();
            $table->string('status_kelulusan')->default('Belum Lulus'); // Belum Lulus, Revisi Sidang, Lulus/Selesai
            $table->date('tanggal_lulus')->nullable();
            $table->string('no_surat_keterangan')->nullable();
            $table->text('catatan_dewan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsis');
    }
};
