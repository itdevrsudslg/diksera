<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankItemRkksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('bank_item_rkks', function (Blueprint $table) {
        $table->id();
        $table->string('kfk')->nullable();
        $table->string('jenjang_klinis')->nullable();
        $table->string('kategori_tugas')->nullable();
        $table->string('no_item')->nullable(); // Pakai string jaga-jaga kalau ada format "1a", "1b"
        $table->text('deskripsi_kompetensi');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_item_rkks');
    }
}
