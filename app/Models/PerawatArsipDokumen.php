<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatArsipDokumen extends Model
{
    use HasFactory;

    protected $table = 'perawat_arsip_dokumen';

    protected $fillable = [
        'user_id',
        'tahun',
        'link_drive',
        'isian_dokumen'
    ];

    // Otomatis mengubah JSON di database menjadi Array di PHP dan sebaliknya
    protected $casts = [
        'isian_dokumen' => 'array',
    ];
}