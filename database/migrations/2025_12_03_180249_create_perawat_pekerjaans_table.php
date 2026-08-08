<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatPekerjaansTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('nama_instansi', 150)->nullable();
            $table->string('jabatan', 150)->nullable();
            $table->string('tahun_mulai', 4)->nullable();
            $table->string('tahun_selesai', 4)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->string('pangkat', 50)->nullable();
            $table->string('status_kepegawaian', 50)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_pekerjaans');
    }
}
