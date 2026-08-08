<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class PerawatDokumenSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');


        $user = User::where('email', 'perawat@gmail')->first();


        $user_id = $user->id;
        $this->command->info('✅ User Ditemukan: ' . $user->name . ' (ID: ' . $user_id . ')');
        $this->command->info('-> Mulai mengisi dokumen...');

        // 2. Insert STR
        DB::table('perawat_strs')->insert([
            'user_id'     => $user_id,
            'nama'        => 'STR Perawat',
            'nomor'       => $faker->numerify('1502#######'),
            'tgl_terbit'  => $faker->date('Y-m-d', '-1 years'),
            'tgl_expired' => $faker->date('Y-m-d', '+4 years'),
            'file_path'   => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $this->command->info('-> Data STR berhasil masuk.');

        // 3. Insert SIP
        DB::table('perawat_sips')->insert([
            'user_id'     => $user_id,
            'nama'        => 'SIP Perawat',
            'nomor'       => $faker->numerify('503/SIP/2025'),
            'tgl_terbit'  => $faker->date('Y-m-d', '-1 years'),
            'tgl_expired' => $faker->date('Y-m-d', '+4 years'),
            'file_path'   => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $this->command->info('-> Data SIP berhasil masuk.');


        // 5. Insert Data Tambahan
        DB::table('perawat_data_tambahans')->insert([
            'user_id'     => $user_id,
            'nama'        => 'NPWP',
            'lembaga'     => 'Pajak',
            'nomor'       => $faker->numerify('01'),
            'tgl_terbit'  => $faker->date('Y-m-d', '-5 years'),
            'tgl_expired' => $faker->date('Y-m-d', '+10 years'),
            'file_path'   => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $this->command->info('-> Data Tambahan berhasil masuk.');

        $this->command->info('🚀 SELESAI! Dokumen sudah ditambahkan ke akun Perawat Contoh.');
    }
}
