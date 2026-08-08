<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatPekerjaan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama_instansi',
        'unit_kerja',
        'jabatan',
        'level_pk',
        'tahun_mulai',
        'tahun_selesai',
        'keterangan',
        'dokumen_path',
        'bidang',
        'kfk',
        'tgl_mulai',
        'tgl_diselenggarakan',
        'pangkat',
        'status_kepegawaian',
        'gol_ruang', 'gaji_pokok', 'pejabat_sk', 'nomor_sk', 'tanggal_sk',
    ];

    /**
     * Get the user that owns the job record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
