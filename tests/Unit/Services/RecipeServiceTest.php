<?php

namespace Tests\Unit\Services;

use App\Enums\RecipeType;
use App\Models\Direction;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Recipe;
use App\Services\DirectionService;
use App\Services\IngredientService;
use App\Services\MealService;
use App\Services\RecipeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeServiceTest extends TestCase
{
    private $service;
    private $mockDirectionService;
    private $mockIngredientService;
    private $mockMealService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockDirectionService = $this->mock(DirectionService::class);
        $this->mockIngredientService = $this->mock(IngredientService::class);
        $this->mockMealService = $this->mock(MealService::class);
        $this->service = $this->partialMockWithConstructor(RecipeService::class);
    }

    public function testCreateRecipe(): void
    {
        Storage::fake('recipeImages');
        $name = $this->faker->word();
        $description = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');
        $type = $this->faker->randomElement(RecipeType::cases());

        $userGroup = $this->user->userGroup;

        $recipe = $this->service->createRecipe($name, $description, $image, $userGroup, $type);

        $this->assertDatabaseHas('recipes', [
            'name' => $name,
            'description' => $description,
            'image' => $recipe->getKey() . '.' . $image->getClientOriginalExtension(),
            'user_group_id' => $userGroup->getKey(),
        ]);
        Storage::disk('recipeImages')->assertExists($recipe->image);
    }

    public function testUpdateRecipe(): void
    {
        Storage::fake('recipeImages');
        $recipe = Recipe::factory()->create();
        $name = $this->faker->word();
        $description = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');
        $type = $this->faker->randomElement(RecipeType::cases());

        $recipe = $this->service->updateRecipe($recipe, $name, $description, $image, $type);

        $this->assertDatabaseHas('recipes', [
            'name' => $name,
            'description' => $description,
            'image' => $recipe->getKey() . '.' . $image->getClientOriginalExtension(),
        ]);
        Storage::disk('recipeImages')->assertExists($recipe->image);
    }

    public function testDeleteRecipe(): void
    {
        $this->mockMealService->shouldReceive('removeRecipe')->once();
        $this->mockDirectionService->shouldReceive('deleteDirection')->once();
        $this->mockIngredientService->shouldReceive('deleteIngredient')->once();
        Storage::fake('recipeImages');
        Storage::disk('recipeImages')->put('image.jpg', '');
        $recipe = Recipe::factory()
            ->has(Direction::factory(), 'directions')
            ->has(Ingredient::factory(), 'ingredients')
            ->create();
        $meal = Meal::factory()->create();
        $meal->recipes()->attach($recipe);
        $this->service->deleteRecipe($recipe);
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->getKey()]);
        Storage::disk('recipeImages')->assertMissing($recipe->image);
    }
}
