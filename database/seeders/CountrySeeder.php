<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::factory(1)->create();
    }
}
