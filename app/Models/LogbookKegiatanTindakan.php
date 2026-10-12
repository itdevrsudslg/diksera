<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookKegiatanTindakan extends Model
{
    protected $fillable = ['kegiatan_id', 'tindakan_id', 'kode', 'nama', 'jumlah'];

    public function kegiatan()
    {
        return $this->belongsTo(LogbookKegiatan::class, 'kegiatan_id');
    }

    public function tindakan()
    {
        return $this->belongsTo(LogbookTindakan::class, 'tindakan_id');
    }
}
