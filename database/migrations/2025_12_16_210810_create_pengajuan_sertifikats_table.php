<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanSertifikatsTable extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_sertifikats', function (Blueprint $table) {
            $table->id();

            // Relasi User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relasi Lisensi Lama (Nullable karena bisa jadi Pengajuan Baru)
            $table->foreignId('lisensi_lama_id')
                ->nullable()
                ->constrained('perawat_lisensis')
                ->onDelete('cascade');

            // Status & Jenis
            $table->string('status')->default('pending');
            $table->enum('jenis_pengajuan', ['baru', 'perpanjangan'])->default('perpanjangan'); // Kolom Baru
            $table->enum('metode', ['pg_only', 'pg_interview', 'interview_only'])->nullable();

            // --- KOLOM FILE BARU ---
            $table->string('link_gdrive')->nullable();           // Link Google Drive
            $table->string('file_dokumen_baru')->nullable();     // PDF (Khusus Pengajuan Baru)
            $table->string('file_sertifikat_lama')->nullable();  // Gambar/PDF (Khusus Perpanjangan)
            $table->string('file_surat_rekomendasi')->nullable(); // Gambar/PDF (Khusus Perpanjangan)

            // --- KOLOM TANGGAL (Diisi Admin) ---
            $table->date('tgl_mulai_berlaku')->nullable();
            $table->date('tgl_akhir_berlaku')->nullable();

            // Kolom Wawancara (Bawaan Lama)
            $table->foreignId('penanggung_jawab_id')->nullable()->constrained('penanggung_jawab_ujians')->onDelete('set null');
            $table->date('tgl_wawancara')->nullable();
            $table->string('lokasi_wawancara')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('pengajuan_sertifikats');
    }
}
