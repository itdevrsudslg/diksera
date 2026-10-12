<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogbookTables extends Migration
{
    public function up()
    {
        // Master tindakan (disinkron dari SIMRS Khanza atau ditambah manual oleh admin)
        Schema::create('logbook_tindakans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30);
            $table->string('nama', 255);
            $table->string('kategori', 20)->default('manual'); // ralan | ranap | manual
            $table->string('sumber', 10)->default('manual');   // khanza | manual
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->unique(['kode', 'kategori']);
            $table->index('nama');
        });

        // Buka/tutup periode bulanan. Default otomatis tutup H+5 setelah akhir bulan; admin bisa override.
        Schema::create('logbook_periodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->string('status_manual', 10)->nullable(); // open | closed | null (otomatis)
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diubah_at')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });

        // Satu entri = satu pasien pada satu hari
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('unit_kerja', 150)->nullable();
            $table->string('no_rm', 30);
            $table->string('nama_pasien', 150)->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('menunggu'); // menunggu | valid | ditolak
            $table->foreignId('validator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('catatan_validator')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'tanggal']);
            $table->index(['unit_kerja', 'tanggal']);
        });

        // Satu pasien banyak kegiatan
        Schema::create('logbook_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('logbook_entries')->cascadeOnDelete();
            $table->string('nama', 255);
            $table->timestamps();
        });

        // Satu kegiatan banyak tindakan (kode & nama disalin agar riwayat tetap utuh)
        Schema::create('logbook_kegiatan_tindakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('logbook_kegiatans')->cascadeOnDelete();
            $table->foreignId('tindakan_id')->nullable()->constrained('logbook_tindakans')->nullOnDelete();
            $table->string('kode', 30);
            $table->string('nama', 255);
            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->timestamps();
        });

        // Rekap bulanan per perawat + 3 tanda tangan elektronik
        Schema::create('logbook_rekaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->string('unit_kerja', 150)->nullable();
            $table->timestamp('perawat_signed_at')->nullable();
            $table->foreignId('karu_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('karu_signed_at')->nullable();
            $table->foreignId('kasi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('kasi_signed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'tahun', 'bulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('logbook_rekaps');
        Schema::dropIfExists('logbook_kegiatan_tindakans');
        Schema::dropIfExists('logbook_kegiatans');
        Schema::dropIfExists('logbook_entries');
        Schema::dropIfExists('logbook_periodes');
        Schema::dropIfExists('logbook_tindakans');
    }
}
