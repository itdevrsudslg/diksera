<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOppeStatusToPerawatProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('perawat_profiles', function (Blueprint $table) {
        // Kita pakai status string: auto, on, off
        $table->string('oppe_status')->default('auto')->after('type_perawat');
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
