<?php

namespace Tests\Feature\Controllers\API;

use App\Models\Meal;
use App\Models\Recipe;
use App\Services\MealService;
use Tests\TestCase;

class MealControllerTest extends TestCase
{
    public $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
        $this->service = $this->mock(MealService::class);
    }

    public function testIndex()
    {
        $this->get(route('api.meals.index', [
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ]))
            ->assertStatus(200);
    }

    public function testShow()
    {
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->get(route('api.meals.show', $meal->getKey()))
            ->assertStatus(200);
    }

    public function testStore()
    {
        $date = $this->faker->date();
        $this->service->shouldReceive('createMeal')->once()->andReturn(Meal::factory()->make());
        $this->post(route('api.meals.store'), [
            'date' => $date,
        ])
            ->assertStatus(201);
    }

    public function testUpdate()
    {
        $this->service->shouldReceive('updateMeal')->once()->andReturn(Meal::factory()->make());
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->put(route('api.meals.update', $meal->getKey()), [
            'date' => $this->faker->date(),
        ])
            ->assertStatus(200);
    }

    public function testDestroy()
    {
        $this->service->shouldReceive('deleteMeal')->once();
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->delete(route('api.meals.destroy', $meal->getKey()))
            ->assertStatus(204);
    }

    public function testAddRecipe()
    {
        $this->service->shouldReceive('addRecipe')->once();
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->post(route('api.meals.add-recipe', [$meal->getKey(), $recipe->getKey()]))
            ->assertStatus(200);
    }

    public function testRemoveRecipe()
    {
        $this->service->shouldReceive('removeRecipe')->once();
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->delete(route('api.meals.remove-recipe', [$meal->getKey(), $recipe->getKey()]))
            ->assertStatus(200);
    }
}
