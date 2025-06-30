<?php

namespace Tests\Unit\Services;

use App\Models\Meal;
use App\Models\Recipe;
use App\Services\MealService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MealServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = resolve(MealService::class);
    }

    public function testCreateMeal()
    {
        $date = Carbon::parse($this->faker->date());

        $meal = $this->service->createMeal($date, $this->user->userGroup);
        $this->assertInstanceOf(Meal::class, $meal);
        $this->assertEquals($date, $meal->date);
        $this->assertEquals($this->user->userGroup->getKey(), $meal->user_group_id);

        $this->assertDatabaseHas('meals', [
            'date' => $date,
            'user_group_id' => $this->user->userGroup->getKey(),
        ]);
    }

    public function testUpdateMeal()
    {
        $date = Carbon::parse($this->faker->date());
        $meal = Meal::factory()->create();

        $this->service->updateMeal($meal, $date);
        $this->assertEquals($date, $meal->date);

        $this->assertDatabaseHas('meals', [
            'id' => $meal->getKey(),
            'date' => $date,
        ]);
    }

    public function testDeleteMeal()
    {
        $meal = Meal::factory()->create();
        $this->service->deleteMeal($meal);
        $this->assertDatabaseMissing('meals', ['id' => $meal->getKey()]);
    }

    public function testAddRecipe()
    {
        $meal = Meal::factory()->create();
        $recipe = Recipe::factory()->create();
        $this->service->addRecipe($meal, $recipe);
        $this->assertDatabaseHas('meal_recipes', [
            'meal_id' => $meal->getKey(),
            'recipe_id' => $recipe->getKey(),
        ]);
    }

    public function testRemoveRecipe()
    {
        $meal = Meal::factory()->create();
        $recipe = Recipe::factory()->create();
        $meal->recipes()->attach($recipe);
        $this->service->removeRecipe($meal, $recipe);
        $this->assertDatabaseMissing('meal_recipes', [
            'meal_id' => $meal->getKey(),
            'recipe_id' => $recipe->getKey(),
        ]);
    }
}
