<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanWawancaraTables extends Migration
{
    public function up()
    {
        Schema::create('jadwal_wawancaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_sertifikat_id')->constrained('pengajuan_sertifikats')->onDelete('cascade');
            $table->foreignId('penanggung_jawab_id')->constrained('penanggung_jawab_ujians');
            $table->dateTime('waktu_wawancara');
            $table->string('lokasi');
            $table->text('deskripsi_skill')->nullable();
            $table->string('status')->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });

        Schema::create('wawancara_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_wawancara_id')->constrained('jadwal_wawancaras')->onDelete('cascade');
            $table->integer('skor_kompetensi')->default(0);
            $table->integer('skor_sikap')->default(0);
            $table->integer('skor_pengetahuan')->default(0);
            $table->text('catatan_pewawancara')->nullable();
            $table->enum('keputusan', ['lulus', 'tidak_lulus']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wawancara_penilaians');
        Schema::dropIfExists('jadwal_wawancaras');
    }
}
