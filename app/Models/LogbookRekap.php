<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LogbookRekap extends Model
{
    protected $fillable = [
        'user_id', 'tahun', 'bulan', 'unit_kerja',
        'perawat_signed_at', 'karu_id', 'karu_signed_at', 'kasi_id', 'kasi_signed_at',
    ];

    protected $casts = [
        'perawat_signed_at' => 'datetime',
        'karu_signed_at' => 'datetime',
        'kasi_signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function karu()
    {
        return $this->belongsTo(User::class, 'karu_id');
    }

    public function kasi()
    {
        return $this->belongsTo(User::class, 'kasi_id');
    }

    public function getNamaPeriodeAttribute(): string
    {
        return Carbon::create($this->tahun, $this->bulan, 1)->translatedFormat('F Y');
    }

    public function getTahapAttribute(): string
    {
        if ($this->kasi_signed_at) return 'selesai';
        if ($this->karu_signed_at) return 'menunggu_kasi';
        if ($this->perawat_signed_at) return 'menunggu_karu';
        return 'draft';
    }
}
