<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurasiToFormsTable extends Migration
{
  public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            // Tambahkan kolom durasi (dalam menit) dengan default 90
            $table->integer('durasi')->default(90)->after('waktu_selesai');
        });
    }

    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('durasi');
        });
    }
}
