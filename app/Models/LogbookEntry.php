<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookEntry extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'unit_kerja', 'no_rm', 'nama_pasien', 'catatan',
        'status', 'validator_id', 'validated_at', 'catatan_validator',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'validated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function kegiatans()
    {
        return $this->hasMany(LogbookKegiatan::class, 'entry_id');
    }

    /** Entri hanya bisa diubah pemiliknya selama belum valid dan periodenya terbuka. */
    public function bisaDiubah(): bool
    {
        return $this->status !== 'valid' && LogbookPeriode::untukTanggal($this->tanggal)->terbuka;
    }

    public function getJumlahTindakanAttribute(): int
    {
        return $this->kegiatans->sum(fn($k) => $k->tindakans->sum('jumlah'));
    }
}
