<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatStr extends Model
{
    use HasFactory;

    protected $table = 'perawat_strs';
    protected $fillable = [
        'user_id',
        'nomor',
        'tgl_terbit',
        'tgl_expired',
        'file_path',
        'kelayakan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
