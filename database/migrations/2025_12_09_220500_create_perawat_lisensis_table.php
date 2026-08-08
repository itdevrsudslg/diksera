<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatLisensisTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_lisensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Data Utama
            $table->string('nama');
            $table->string('lembaga');
            $table->string('nomor');

            // Detail Tambahan (Bidang, KFK, dll)
            $table->string('bidang')->nullable();
            $table->text('kfk')->nullable(); // Gunakan TEXT/JSON untuk array
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_diselenggarakan')->nullable();

            // Tanggal Berlaku
            $table->date('tgl_terbit');
            $table->date('tgl_expired');

            $table->string('file_path')->nullable();

            // KOLOM PENTING 1: Metode (Nama kolom di DB: metode)
            // Enum hanya menerima nilai ini. Untuk lisensi baru, controller akan tetap mengisi salah satu dari ini untuk arsip.
            $table->enum('metode', ['pg_only', 'pg_interview', 'interview_only'])->nullable();

            $table->string('unit_kerja_saat_buat')->nullable();

            // KOLOM PENTING 2: Status Approval
            $table->enum('status', ['pending', 'active', 'rejected', 'expired'])->default('pending');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_lisensis');
    }
}
