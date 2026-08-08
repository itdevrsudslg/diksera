<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatKeluarga extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'hubungan',
        'nama',
        'tanggal_lahir',
        'pekerjaan',
        'tempat_lahir', 'jenis_kelamin', 'tanggal_nikah', 'keterangan', 'no_darurat',
    ];

    /**
     * Get the user that owns the family record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}