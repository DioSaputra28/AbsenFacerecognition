<?php

namespace Database\Seeders; // Pastikan namespace ini benar

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder; // Tambahkan use statement untuk UserSeeder jika diperlukan

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Menyebutkan UserSeeder untuk di-seed
        $this->call(UserSeeder::class);
    }
}
