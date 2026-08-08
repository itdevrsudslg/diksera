<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin (Otomatis Active)
        User::updateOrCreate(
            ['email' => 'admin@gmail'],
            [
                'name'             => 'Admin DIKSERA',
                'password'         => Hash::make('password'),
                'role'             => 'admin',
                'telegram_chat_id' => null,
                'status_akun'      => 'active',
            ]
        );
        
         User::updateOrCreate(
            ['email' => 'mg.satriya@gmail'],
            [
                'name'             => 'Moch. Gandung Satriya',
                'password'         => Hash::make('password'),
                'role'             => 'admin',
                'telegram_chat_id' => null,
                'status_akun'      => 'active',
            ]
        );

        // // 2. Contoh Perawat (Otomatis Active)
        // User::updateOrCreate(
        //     ['email' => 'perawat@gmail'],
        //     [
        //         'name'             => 'Perawat Contoh',
        //         'password'         => Hash::make('password'),
        //         'role'             => 'perawat',
        //         'telegram_chat_id' => null,
        //         'status_akun'      => 'active',
        //     ]
        // );

        // // 3. Contoh Pewawancara (Otomatis Active)
        // User::updateOrCreate(
        //     ['email' => 'pewawancara@gmail'],
        //     [
        //         'name'             => 'Pewawancara Contoh',
        //         'password'         => Hash::make('password'),
        //         'role'             => 'pewawancara',
        //         'telegram_chat_id' => null,
        //         'status_akun'      => 'active',
        //     ]
        // );

        // // 4. Perawat Satu
        // User::updateOrCreate(
        //     ['email' => 'perawat1@gmail'],
        //     [
        //         'name'             => 'Perawat Satu',
        //         'password'         => Hash::make('password'),
        //         'role'             => 'perawat',
        //         'telegram_chat_id' => null,
        //         'status_akun'      => 'active',
        //     ]
        // );

        // // 5. Perawat Dua
        // User::updateOrCreate(
        //     ['email' => 'perawat2@gmail'],
        //     [
        //         'name'             => 'Perawat Dua',
        //         'password'         => Hash::make('password'),
        //         'role'             => 'perawat',
        //         'telegram_chat_id' => null,
        //         'status_akun'      => 'active',
        //     ]
        // );

        // // 6. Perawat Tiga
        // User::updateOrCreate(
        //     ['email' => 'perawat3@gmail'],
        //     [
        //         'name'             => 'Perawat Tiga',
        //         'password'         => Hash::make('password'),
        //         'role'             => 'perawat',
        //         'telegram_chat_id' => null,
        //         'status_akun'      => 'active',
        //     ]
        // );
    }
}
