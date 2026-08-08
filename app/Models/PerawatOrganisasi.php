<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatOrganisasi extends Model
{
    protected $fillable = [
        'user_id',
        'nama_organisasi',
        'jabatan',
        'tempat',           // <-- TAMBAH
        'pemimpin',         // <-- TAMBAH
        'dokumen_path',
        'tahun_mulai',
        'tahun_selesai',
        // Unused 
        'keterangan',
        'masa_organisasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
