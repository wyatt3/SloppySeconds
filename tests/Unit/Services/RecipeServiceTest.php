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

    public function testCreateDirection(): void
    {
        Storage::fake('recipeImages');
        $content = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');
        $recipe = Recipe::factory()->create();

        $direction = $this->service->createDirection($content, $image, $recipe);

        $this->assertDatabaseHas('directions', [
            'content' => $content,
            'image' => $direction->getKey() . '.' . $image->getClientOriginalExtension(),
            'recipe_id' => $recipe->getKey(),
        ]);
        Storage::disk('recipeImages')->assertExists("directions/{$direction->getKey()}.{$image->getClientOriginalExtension()}");
    }

    public function testUpdateDirection(): void
    {
        Storage::fake('recipeImages');
        $direction = Direction::factory()->create();
        $content = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');

        $direction = $this->service->updateDirection($direction, $content, $image);

        $this->assertDatabaseHas('directions', [
            'content' => $content,
            'image' => $direction->getKey() . '.' . $image->getClientOriginalExtension(),
        ]);
        Storage::disk('recipeImages')->assertExists("directions/{$direction->getKey()}.{$image->getClientOriginalExtension()}");
    }

    public function testUpdateDirectionOrder(): void
    {
        $direction = Direction::factory()->create(['order' => 0]);
        $order = 1;

        $direction = $this->service->updateDirectionOrder($direction, $order);

        $this->assertEquals($order, $direction->order);
        $this->assertDatabaseHas('directions', [
            'id' => $direction->getKey(),
            'order' => $order
        ]);
    }

    public function testDeleteDirection(): void
    {
        Storage::fake('recipeImages');
        $direction = Direction::factory()->create();
        $this->service->deleteDirection($direction);
        $this->assertDatabaseMissing('directions', ['id' => $direction->getKey()]);
        Storage::disk('recipeImages')->assertMissing("directions/{$direction->getKey()}.image.jpg");
    }
}
