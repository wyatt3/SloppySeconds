<?php

namespace Database\Factories;

use App\Enums\RecipeType;
use App\Models\UserGroup;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    use HasImages;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var RecipeType $type */
        $type = $this->faker->randomElement(RecipeType::cases());
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'image' => $this->saveRandomImage('recipeImages', $this->faker->word()),
            'user_group_id' => UserGroup::factory(),
            'type' => $type->name,
        ];
    }
}
