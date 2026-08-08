<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatDataTambahan extends Model
{
    use HasFactory;

    protected $table = 'perawat_data_tambahans';
protected $fillable = [
        'user_id',
        'nomor',
        'nama',
        'lembaga',
        'tgl_terbit',
        'tgl_expired',
        'skp', // <-- TAMBAHKAN INI
        'is_lifetime',
        'lifetime_approved',
        'file_path',
        'kelayakan',
    ];

    protected $casts = [
        'tgl_terbit' => 'date',
        'tgl_expired' => 'date',
        'is_lifetime' => 'boolean',
        'lifetime_approved' => 'boolean',
        'lifetime_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
