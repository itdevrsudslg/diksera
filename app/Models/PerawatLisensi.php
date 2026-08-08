<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatLisensi extends Model
{
    use HasFactory;

    protected $table = 'perawat_lisensis';

    // Sesuaikan dengan kolom migration
    protected $fillable = [
        'user_id',
        'nomor',
        'nama',
        'lembaga',
        'tgl_terbit',
        'tgl_expired',
        'file_path',
        'metode', // Nama kolom di DB adalah 'metode'
        'bidang',
        'kfk',
        'tgl_mulai',
        'tgl_diselenggarakan',
        'unit_kerja_saat_buat',
        'status',
    ];

    protected $casts = [
        'kfk' => 'array',
    ];

    // Relasi
    public function pengajuans()
    {
        return $this->hasMany(PengajuanSertifikat::class, 'lisensi_lama_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
public function pengajuanTerkait()
    {
        // Cari pengajuan yang terhubung langsung dengan lisensi ini
        // Menggunakan ID lisensi_lama_id yang diset di finalizeLisensi
        return \App\Models\PengajuanSertifikat::where('lisensi_lama_id', $this->id)
            ->orWhere('user_id', $this->user_id) // Fallback jika tidak terhubung langsung
            ->latest()
            ->first();
    }
    /**
     * LOGIC RESET DATA (HAPUS ANAK DATA OTOMATIS)
     */
    protected static function boot()
    {
        parent::boot();

        // 1. SAAT LISENSI DIHAPUS ADMIN
        static::deleting(function ($lisensi) {
            $lisensi->pengajuans()->each(function ($pengajuan) {
                $pengajuan->delete();
            });
        });

        // 2. SAAT STATUS BERUBAH JADI EXPIRED / REJECTED
        static::updated(function ($lisensi) {
            if ($lisensi->isDirty('status') && in_array($lisensi->status, ['expired', 'rejected'])) {
                $lisensi->pengajuans()->each(function ($pengajuan) {
                    $pengajuan->delete();
                });
            }
        });
    }
}
