<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatProfile extends Model
{
    protected $table = 'perawat_profiles';

    protected $fillable = [
        'user_id',
        'is_oppe_manual_open',
        'profesi',              // <--- TAMBAH INI
    'status_kepegawaian',
    'oppe_status',// <--- TAMBAH INI
        // Identitas Utama
        'nik',
        'nip',
        'nirp',
        'nama_lengkap',
'jabatan_struktural',
        // Biodata
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'aliran_kepercayaan',
        'status_perkawinan',

        // Jabatan & Pangkat
        'jabatan',
        'pangkat',
        'golongan',

        // Kontak
        'alamat',
        'kota',
        'no_hp',

        // Fisik
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'rambut',
        'bentuk_muka',
        'warna_kulit',
        'ciri_khas',
        'cacat_tubuh',
        'hobby',
        'foto_3x4',

        // Tipe Perawat
        'type_perawat',
        
        'npwp', 'tmt_cpns', 'tmt_pns', 'tmt_jabatan', 'masa_kerja_tahun', 'masa_kerja_bulan', 
'kelurahan_desa', 'kecamatan', 'skck_pejabat', 'skck_nomor', 'skck_tanggal', 
'sehat_pejabat', 'sehat_nomor', 'sehat_tanggal', 'ket_lain_pejabat', 'ket_lain_nomor', 'ket_lain_tanggal',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
