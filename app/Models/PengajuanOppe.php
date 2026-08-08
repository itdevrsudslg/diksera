<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanOppe extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_oppes';

    protected $fillable = [
        'user_id',
        'peer_id',
        'evaluator_id',
        'lisensi_id',
        'periode',
        'status',
        'instrumen_penilaian',
        'catatan_evaluator',
        'nilai_akhir'
    ];

    // Cast kolom instrumen_penilaian agar otomatis menjadi Array/JSON
    protected $casts = [
        'instrumen_penilaian' => 'array',
        'nilai_akhir' => 'decimal:2'
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function peer()
    {
        return $this->belongsTo(User::class, 'peer_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function lisensi()
    {
        return $this->belongsTo(PerawatLisensi::class, 'lisensi_id');
    }
}