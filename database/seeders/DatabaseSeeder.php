<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Panggil seeder user
        $this->call([
            UserSeeder::class,
            // PerawatDokumenSeeder::class,
            // PerawatDataDiriSeeder::class,
            // PenanggungJawabUjianSeeder::class,
            // FormSeeder::class,
        ]);
    }
}
