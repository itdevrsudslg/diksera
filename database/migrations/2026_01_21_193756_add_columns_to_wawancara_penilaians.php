<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWawancaraPenilaians extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            $table->string('file_hasil')->nullable()->after('keputusan');
            $table->json('detail_penilaian')->nullable()->after('file_hasil');
        });
    }

    public function down()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            $table->dropColumn(['file_hasil', 'detail_penilaian']);
        });
    }
}
