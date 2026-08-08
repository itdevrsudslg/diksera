<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read'
    ];

    // Opsional: Tambahkan relasi agar mudah memanggil nama pengirim
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}