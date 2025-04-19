<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Direction>
 */
class DirectionFactory extends Factory
{
    use HasImages;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->text(50),
            'recipe_id' => Recipe::factory(),
            'order' => $this->faker->numberBetween(1, 10),
            'image' => $this->saveRandomImage('recipeImages', "directions/" . $this->faker->word()),
        ];
    }
}
