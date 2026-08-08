<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            // Menambahkan kolom baru setelah NIK
            $table->string('profesi', 50)->nullable()->after('nik'); 
            $table->string('status_kepegawaian', 50)->nullable()->after('profesi');
        });
    }

    public function down()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            $table->dropColumn(['profesi', 'status_kepegawaian']);
        });
    }
};