<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'recipe_id' => Recipe::factory(),
            'amount' => $this->faker->numberBetween(1, 10),
            'unit' => $this->faker->word(),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
