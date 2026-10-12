<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookTindakan extends Model
{
    protected $fillable = ['kode', 'nama', 'kategori', 'sumber', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }

    public function getLabelAttribute()
    {
        return $this->kode . ' - ' . $this->nama;
    }
}
