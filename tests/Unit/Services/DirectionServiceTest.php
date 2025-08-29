<?php

namespace Tests\Unit\Services;

use App\Models\Direction;
use App\Models\Recipe;
use App\Services\DirectionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DirectionServiceTest extends TestCase
{
    private $service;
    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->partialMockWithConstructor(DirectionService::class);
    }

    public function testCreateDirection(): void
    {
        Storage::fake('recipeImages');
        $title = $this->faker->word();
        $content = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');
        $recipe = Recipe::factory()->create();

        $direction = $this->service->createDirection($title, $content, $image, $recipe);

        $this->assertDatabaseHas('directions', [
            'title' => $title,
            'content' => $content,
            'image' => $direction->getKey() . '.' . $image->getClientOriginalExtension(),
            'recipe_id' => $recipe->getKey(),
        ]);
        Storage::disk('recipeImages')->assertExists("directions/{$direction->getKey()}.{$image->getClientOriginalExtension()}");
    }

    public function testUpdateDirection(): void
    {
        Storage::fake('recipeImages');
        $title = $this->faker->word();
        $direction = Direction::factory()->create();
        $content = $this->faker->sentence();
        $image = UploadedFile::fake()->image('image.jpg');

        $direction = $this->service->updateDirection($direction, $title, $content, $image);

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

        $this->service->updateDirectionOrder($direction, $order);

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
