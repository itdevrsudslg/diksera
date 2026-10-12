<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LogbookPeriode extends Model
{
    /** Jumlah hari setelah akhir bulan sebelum periode tertutup otomatis. */
    const HARI_TOLERANSI = 5;

    protected $fillable = ['tahun', 'bulan', 'status_manual', 'diubah_oleh', 'diubah_at', 'catatan'];

    protected $casts = ['diubah_at' => 'datetime'];

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public static function untuk(int $tahun, int $bulan): self
    {
        return static::firstOrNew(['tahun' => $tahun, 'bulan' => $bulan]);
    }

    public static function untukTanggal($tanggal): self
    {
        $d = Carbon::parse($tanggal);
        return static::untuk($d->year, $d->month);
    }

    public function getAwalAttribute(): Carbon
    {
        return Carbon::create($this->tahun, $this->bulan, 1)->startOfDay();
    }

    /** Batas pengisian otomatis: akhir bulan + H+5 (sampai pukul 23:59). */
    public function getBatasAttribute(): Carbon
    {
        return $this->awal->copy()->endOfMonth()->addDays(self::HARI_TOLERANSI)->endOfDay();
    }

    public function getTerbukaAttribute(): bool
    {
        if ($this->status_manual === 'open') return true;
        if ($this->status_manual === 'closed') return false;
        $now = now();
        return $now->gte($this->awal) && $now->lte($this->batas);
    }

    public function getNamaAttribute(): string
    {
        return $this->awal->translatedFormat('F Y');
    }

    public function getKeteranganStatusAttribute(): string
    {
        if ($this->status_manual === 'open') return 'Dibuka manual oleh admin';
        if ($this->status_manual === 'closed') return 'Ditutup manual oleh admin';
        if (now()->lt($this->awal)) return 'Belum dimulai';
        return $this->terbuka
            ? 'Otomatis, tutup ' . $this->batas->translatedFormat('d M Y')
            : 'Tutup otomatis sejak ' . $this->batas->translatedFormat('d M Y');
    }
}
