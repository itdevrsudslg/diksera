<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanOppesTable extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_oppes', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke Teman Sejawat & Evaluator (Karu/Kasi)
            $table->foreignId('peer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Relasi ke Lisensi Aktif saat OPPE diajukan (untuk tracking KFK & Jenjang)
            $table->foreignId('lisensi_id')->nullable()->constrained('perawat_lisensis')->onDelete('set null');
            
            // Data OPPE
            $table->string('periode', 20); // Contoh: "2025-2026"
            $table->enum('status', [
                'draft', 
                'pending_self', 
                'pending_peer', 
                'pending_karu', 
                'completed'
            ])->default('draft');
            
            // Snapshot Butir Soal dan Skor dari 3 Pihak disimpan dalam bentuk JSON
            $table->json('instrumen_penilaian')->nullable();
            
            $table->text('catatan_evaluator')->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable(); // Menyimpan persentase akhir (misal: 97.04)
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_oppes');
    }
}
