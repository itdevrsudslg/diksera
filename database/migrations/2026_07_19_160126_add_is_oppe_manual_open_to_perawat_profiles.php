<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOppeManualOpenToPerawatProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
{
    Schema::table('perawat_profiles', function (Blueprint $table) {
        $table->boolean('is_oppe_manual_open')->default(false)->after('type_perawat');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            //
        });
    }
}
