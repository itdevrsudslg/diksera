<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJabatanStrukturalToPerawatProfilesTable extends Migration
{
   public function up()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            // Menambahkan kolom jabatan struktural setelah kolom jabatan fungsional
            $table->enum('jabatan_struktural', [
                'pelaksana', 
                'kepala_ruangan', 
                'kepala_seksi', 
                'komite'
            ])->default('pelaksana')->after('jabatan');
        });
    }

    public function down()
    {
        Schema::table('perawat_profiles', function (Blueprint $table) {
            $table->dropColumn('jabatan_struktural');
        });
    }
}
