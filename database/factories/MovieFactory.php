<?php

namespace Database\Factories;

use App\Enums\MovieTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = array_column(MovieTypeEnum::cases(), 'value');

        return [
            'title' => fake()->title(),
            'release_date' => fake()->date(),
            'duration' => fake()->numberBetween(40, 180),
            'short_content' => fake()->text(255),
            'description' => fake()->realText(1000),
            'poster_url' => 'images/poster2.jpg',
            'video_url' => 'https://vimeo.com/1021066385',
            'region_id' => '1',
            'type' => $types[array_rand($types)],
            'is_active' => '1',
            'views' => fake()->numberBetween(1, 1000),
        ];
    }
}
