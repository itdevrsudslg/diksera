<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkpToPerawatPelatihansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
{
    Schema::table('perawat_pelatihans', function (Blueprint $table) {
        $table->integer('skp')->default(0)->after('dokumen_path')->comment('Jumlah Satuan Kredit Profesi');
    });
}

public function down(): void
{
    Schema::table('perawat_pelatihans', function (Blueprint $table) {
        $table->dropColumn('skp');
    });
}
}
