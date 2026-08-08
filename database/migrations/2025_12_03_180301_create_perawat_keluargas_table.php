<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatKeluargasTable extends Migration
{
    public function up()
    {
        Schema::create('perawat_keluargas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // suami_istri / anak / orang_tua / saudara_kandung / mertua / dll
            $table->string('hubungan', 50)->nullable();
            $table->string('nama', 150)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan', 150)->nullable();
             $table->string('no_darurat', 20)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_keluargas');
    }
}
