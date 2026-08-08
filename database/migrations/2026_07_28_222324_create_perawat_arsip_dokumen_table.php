<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatArsipDokumenTable extends Migration
{
   public function up()
    {
        Schema::create('perawat_arsip_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tahun', 4);
            $table->text('link_drive');
            $table->json('isian_dokumen')->nullable(); // Disimpan sebagai JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawat_arsip_dokumen');
    }
}
