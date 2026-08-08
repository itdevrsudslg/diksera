<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WawancaraPenilaian extends Model
{
    protected $guarded = [];

    // Tambahkan baris ini
    protected $casts = [
        'detail_penilaian' => 'array',
        'nilai_pertanyaan' => 'array',
        'uji_skill'        => 'array',
    ];

    public function jadwal() {
        return $this->belongsTo(JadwalWawancara::class, 'jadwal_wawancara_id');
    }
}