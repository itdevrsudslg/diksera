<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkpToPerawatDataTambahansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
{
    Schema::table('perawat_data_tambahans', function (Blueprint $table) {
        $table->integer('skp')->default(0)->after('tgl_expired')->comment('Total SKP Dokumen');
    });
}

public function down(): void
{
    Schema::table('perawat_data_tambahans', function (Blueprint $table) {
        $table->dropColumn('skp');
    });
}
}
