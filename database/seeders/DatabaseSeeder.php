<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CategorySeeder::class,
            MovieSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
