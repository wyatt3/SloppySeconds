<?php

namespace Tests\Unit\Services;

use App\Models\Direction;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeServiceTest extends TestCase
{
    private $service;
    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->partialMockWithConstructor(RecipeService::class);
    }

    public function testCreateRecipe(): void
    {
        Storage::fake('recipeImages');
        $name = $this->faker->word();
        $description = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');

        $userGroup = $this->user->userGroup;

        $recipe = $this->service->createRecipe($name, $description, $image, $userGroup);

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

        $recipe = $this->service->updateRecipe($recipe, $name, $description, $image);

        $this->assertDatabaseHas('recipes', [
            'name' => $name,
            'description' => $description,
            'image' => $recipe->getKey() . '.' . $image->getClientOriginalExtension(),
        ]);
        Storage::disk('recipeImages')->assertExists($recipe->image);
    }

    public function testDeleteRecipe(): void
    {
        Storage::fake('recipeImages');
        Storage::disk('recipeImages')->put('image.jpg', '');
        $recipe = Recipe::factory()->create();
        $this->service->deleteRecipe($recipe);
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->getKey()]);
        Storage::disk('recipeImages')->assertMissing($recipe->image);
    }
}
