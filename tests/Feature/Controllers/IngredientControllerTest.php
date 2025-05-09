<?php

namespace Tests\Feature\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Services\IngredientService;
use Tests\TestCase;

class IngredientControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
        $this->service = $this->mock(IngredientService::class);
    }

    public function testStore()
    {
        $recipe = Recipe::factory()->for($this->user->userGroup)->create();
        $this->service->shouldReceive('createIngredient')->once()->andReturn(Ingredient::factory()->create());
        $response = $this->post(route('api.ingredients.store', ['recipe' => $recipe->getKey()]), [
            'name' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2),
            'unit' => $this->faker->word(),
        ]);
        $response->assertCreated();
    }

    public function testUpdate()
    {
        $ingredient = Ingredient::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->create();
        $this->service->shouldReceive('updateIngredient')->once()->andReturn($ingredient);
        $response = $this->put(route('api.ingredients.update', ['recipe' => $ingredient->recipe->getKey(), 'ingredient' => $ingredient->getKey()]), [
            'name' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2),
            'unit' => $this->faker->word(),
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateOrders()
    {
        $ingredients = Ingredient::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->count(3)->create();
        $this->service->shouldReceive('updateIngredientOrder')->times(3);
        $response = $this->put(route('api.ingredients.update-orders', ['recipe' => $ingredients[0]->recipe->getKey()]), [
            'ingredients' => [
                ['id' => $ingredients[0]->getKey(), 'order' => 1],
                ['id' => $ingredients[1]->getKey(), 'order' => 2],
                ['id' => $ingredients[2]->getKey(), 'order' => 3],
            ],
        ]);
        $response->assertNoContent();
    }

    public function testDelete()
    {
        $ingredient = Ingredient::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->create();
        $this->service->shouldReceive('deleteIngredient')->once();
        $response = $this->delete(route('api.ingredients.delete', ['recipe' => $ingredient->recipe->getKey(), 'ingredient' => $ingredient->getKey()]));
        $response->assertNoContent();
    }
}
