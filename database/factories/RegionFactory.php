<?php

namespace Database\Factories;

use App\Enums\MovieTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class RegionFactory extends Factory
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
            'name' => "O'zbekiston"
        ];
    }
}
