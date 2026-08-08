<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatSip extends Model
{
    use HasFactory;

    protected $table = 'perawat_sips';
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
