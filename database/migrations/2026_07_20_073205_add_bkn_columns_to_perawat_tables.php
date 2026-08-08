<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBknColumnsToPerawatTables extends Migration
{
 public function up()
    {
        // 1. Update Profiles
        Schema::table('perawat_profiles', function (Blueprint $table) {
            $table->string('npwp', 50)->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->string('kelurahan_desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('skck_pejabat', 150)->nullable();
            $table->string('skck_nomor', 100)->nullable();
            $table->date('skck_tanggal')->nullable();
            $table->string('sehat_pejabat', 150)->nullable();
            $table->string('sehat_nomor', 100)->nullable();
            $table->date('sehat_tanggal')->nullable();
            $table->string('ket_lain_pejabat', 150)->nullable();
            $table->string('ket_lain_nomor', 100)->nullable();
            $table->date('ket_lain_tanggal')->nullable();
        });

        // 2. Update Pendidikans
        Schema::table('perawat_pendidikans', function (Blueprint $table) {
            $table->string('nama_kepala_institusi', 150)->nullable();
        });

        // 3. Update Pekerjaans
        Schema::table('perawat_pekerjaans', function (Blueprint $table) {
            $table->string('gol_ruang', 50)->nullable();
            $table->string('gaji_pokok', 100)->nullable();
            $table->string('pejabat_sk', 150)->nullable();
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
        });

        // 4. Update Keluargas
        Schema::table('perawat_keluargas', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal_nikah')->nullable();
            $table->string('keterangan', 255)->nullable();
        });

        // 5. Update Organisasi
        Schema::table('perawat_organisasis', function (Blueprint $table) {
            // Pilihan: SLTA, PT, Bekerja
            $table->string('masa_organisasi', 50)->nullable();
        });

        // 6. Buat Tabel Baru: Kunjungan Luar Negeri
        Schema::create('perawat_kunjungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('negara', 100);
            $table->string('tujuan_kunjungan', 255);
            $table->string('lamanya', 100)->nullable();
            $table->string('yang_membiayai', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            $table->dropColumn(['npwp', 'tmt_cpns', 'tmt_pns', 'tmt_jabatan', 'masa_kerja_tahun', 'masa_kerja_bulan', 'kelurahan_desa', 'kecamatan', 'skck_pejabat', 'skck_nomor', 'skck_tanggal', 'sehat_pejabat', 'sehat_nomor', 'sehat_tanggal', 'ket_lain_pejabat', 'ket_lain_nomor', 'ket_lain_tanggal']);
        });

        Schema::table('perawat_pendidikans', function (Blueprint $table) {
            $table->dropColumn('nama_kepala_institusi');
        });

        Schema::table('perawat_pekerjaans', function (Blueprint $table) {
            $table->dropColumn(['gol_ruang', 'gaji_pokok', 'pejabat_sk', 'nomor_sk', 'tanggal_sk']);
        });

        Schema::table('perawat_keluargas', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'jenis_kelamin', 'tanggal_nikah', 'keterangan']);
        });

        Schema::table('perawat_organisasis', function (Blueprint $table) {
            $table->dropColumn('masa_organisasi');
        });

        Schema::dropIfExists('perawat_kunjungans');
    }
}
