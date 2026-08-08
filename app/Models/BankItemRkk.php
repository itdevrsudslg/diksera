<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankItemRkk extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'kfk', 
        'jenjang_klinis', 
        'kategori_tugas', 
        'no_item', 
        'deskripsi_kompetensi'
    ];
}