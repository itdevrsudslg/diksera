<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelPkToPerawatPekerjaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
    {
        Schema::table('perawat_pekerjaans', function (Blueprint $table) {
            // Menambahkan kolom level_pk setelah kolom jabatan
            $table->string('level_pk', 50)->nullable()->after('jabatan')->comment('Jenjang PK saat di instansi ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perawat_pekerjaans', function (Blueprint $table) {
            $table->dropColumn('level_pk');
        });
}}
