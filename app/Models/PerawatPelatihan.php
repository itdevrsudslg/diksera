<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatPelatihan extends Model
{
    protected $fillable = [
        'user_id',
        'nama_pelatihan',
        'penyelenggara',
        'tempat',           // <-- TAMBAH
        'durasi',
        'tanggal_mulai',    // <-- TAMBAH
        'tanggal_selesai',  // <-- TAMBAH
        'dokumen_path',     // <-- TAMBAH
        'tahun',    
        'skp',// Tetap ada (unused)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}