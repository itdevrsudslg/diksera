<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // Identitas
            $table->string('nik', 30)->nullable();
            $table->string('nip', 50)->nullable();
            $table->string('nirp', 50)->nullable();

            $table->string('nama_lengkap', 150);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable(); // L / P
            $table->string('agama', 30)->nullable();
            $table->string('aliran_kepercayaan', 100)->nullable();
            $table->string('status_perkawinan', 50)->nullable();

            // Jabatan
            $table->string('jabatan', 100)->nullable();
            $table->string('pangkat', 50)->nullable();
            $table->string('golongan', 20)->nullable();

            // Alamat & kontak
            $table->string('alamat', 255)->nullable(); // gabungan jalan+kel+dsb
            $table->string('kota', 100)->nullable();
            $table->string('no_hp', 30)->nullable();

            // Keterangan badan
            $table->string('golongan_darah', 20)->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();

            $table->string('rambut', 100)->nullable();
            $table->string('bentuk_muka', 100)->nullable();
            $table->string('warna_kulit', 100)->nullable();
            $table->string('ciri_khas', 150)->nullable();
            $table->string('cacat_tubuh', 150)->nullable();

            // Lain-lain
            $table->string('hobby', 150)->nullable();
            $table->string('foto_3x4', 255)->nullable(); // path di storage

            // Tipe Perawat (hardcode kategori soal)
            $table->string('type_perawat', 50)->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_profiles');
    }
}
