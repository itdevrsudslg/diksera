<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookKegiatan extends Model
{
    protected $fillable = ['entry_id', 'nama'];

    public function entry()
    {
        return $this->belongsTo(LogbookEntry::class, 'entry_id');
    }

    public function tindakans()
    {
        return $this->hasMany(LogbookKegiatanTindakan::class, 'kegiatan_id');
    }
}
