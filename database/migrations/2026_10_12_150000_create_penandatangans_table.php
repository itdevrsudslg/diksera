<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenandatangansTable extends Migration
{
    public function up()
    {
        // Riwayat pejabat penanda tangan dokumen (Direktur, Ketua Komite, dst).
        // Dokumen memakai pejabat yang berlaku pada tanggal dokumen; tanpa data memakai nilai bawaan.
        Schema::create('penandatangans', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 50);
            $table->string('nama', 150);
            $table->string('nip', 50)->nullable();
            $table->string('pangkat', 100)->nullable();
            $table->date('berlaku_mulai');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['slot', 'berlaku_mulai']);
        });

        // Pengaturan teks sederhana (key-value), mis. label kolom TTD OPPE & logbook
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 100)->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengaturans');
        Schema::dropIfExists('penandatangans');
    }
}
