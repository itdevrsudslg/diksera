<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatKunjungan extends Model
{
    protected $table = 'perawat_kunjungans';
    protected $fillable = [
        'user_id', 'negara', 'tujuan_kunjungan', 'lamanya', 'yang_membiayai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}