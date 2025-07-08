<?php

namespace Tests\Unit\Services;

use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Recipe;
use App\Models\UserGroup;
use App\Services\ShoppingListService;
use Tests\TestCase;

class ShoppingListServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->partialMockWithConstructor(ShoppingListService::class);
    }

    public function testGenerateShoppingList()
    {
        $this->service->shouldReceive('combineLikeIngredients')->once()->andReturn(collect());

        $userGroup = UserGroup::factory()->create();
        $meal = Meal::factory()->has(Recipe::factory()->has(Ingredient::factory()->count(3)))->create([
            'user_group_id' => $userGroup->getKey(),
            'date' => now(),
        ]);



        $response = $this->service->generateShoppingList(now()->subDays(1), now()->addDays(1), $userGroup);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $response);
        $this->assertCount(0, $response);
    }

    public function testCombineLikeIngredients()
    {
        $list = collect();
        $ingredients = collect();
    }
}
