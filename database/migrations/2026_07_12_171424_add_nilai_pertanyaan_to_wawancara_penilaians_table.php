<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiPertanyaanToWawancaraPenilaiansTable extends Migration
{
public function up()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            // Menambahkan kolom JSON setelah kolom catatan
            $table->json('nilai_pertanyaan')->nullable()->after('catatan_pewawancara');
        });
    }

    public function down()
    {
        Schema::table('wawancara_penilaians', function (Blueprint $table) {
            $table->dropColumn('nilai_pertanyaan');
        });
    }
}
