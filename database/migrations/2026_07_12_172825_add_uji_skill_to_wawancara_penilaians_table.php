<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUjiSkillToWawancaraPenilaiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            $table->json('uji_skill')->nullable()->after('nilai_pertanyaan');
        });
    }

    public function down()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            $table->dropColumn('uji_skill');
        });
    }
}
