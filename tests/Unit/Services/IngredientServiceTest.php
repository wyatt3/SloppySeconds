<?php

namespace Tests\Unit\Services;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Services\IngredientService;
use Tests\TestCase;

class IngredientServiceTest extends TestCase
{
    private $ingredientService;

    public function setUp(): void
    {
        parent::setUp();
        $this->ingredientService = $this->partialMockWithConstructor(IngredientService::class);
    }

    public function testCreateIngredient()
    {
        $recipe = Recipe::factory()->create();
        $name = $this->faker->word();
        $amount = $this->faker->numberBetween(1, 10);
        $unit = $this->faker->word();

        $ingredient = $this->ingredientService->createIngredient($recipe, $name, $amount, $unit);

        $this->assertDatabaseHas('ingredients', [
            'name' => $name,
            'recipe_id' => $recipe->getKey(),
            'amount' => $amount,
            'unit' => $unit,
        ]);
    }

    public function testUpdateIngredient()
    {
        $ingredient = Ingredient::factory()->create();
        $name = $this->faker->word();
        $amount = $this->faker->numberBetween(1, 10);
        $unit = $this->faker->word();

        $ingredient = $this->ingredientService->updateIngredient($ingredient, $name, $amount, $unit);

        $this->assertDatabaseHas('ingredients', [
            'name' => $name,
            'amount' => $amount,
            'unit' => $unit,
        ]);
    }

    public function testUpdateIngredientOrder()
    {
        $ingredient = Ingredient::factory()->create();
        $order = $this->faker->numberBetween(1, 10);

        $this->ingredientService->updateIngredientOrder($ingredient, $order);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->getKey(),
            'order' => $order
        ]);
    }

    public function testDeleteIngredient()
    {
        $ingredient = Ingredient::factory()->create();
        $this->ingredientService->deleteIngredient($ingredient);
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->getKey()]);
    }
}
