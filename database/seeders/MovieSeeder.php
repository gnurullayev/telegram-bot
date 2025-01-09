<?php

namespace Database\Seeders;

use App\Enums\MovieTypeEnum;
use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::factory(50)->create()->each(function ($movie) {
            $movie->category_id =  rand(1, 9);

            $movie->save();
        });
    }
}
