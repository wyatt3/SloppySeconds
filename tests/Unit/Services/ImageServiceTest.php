<?php

namespace Tests\Unit\Services;

use App\Models\Direction;
use App\Models\Recipe;
use App\Services\ImageService;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->partialMockWithConstructor(ImageService::class);
    }

    public function testGetRecipeImage(): void
    {
        Storage::fake('recipeImages');
        $recipe = Recipe::factory()->create();
        $mockDisk = $this->mock(LocalFilesystemAdapter::class);
        Storage::shouldReceive('disk')->twice()->with('recipeImages')->andReturn($mockDisk);

        $mockDisk->shouldReceive('path')->once()->with('default.jpg')->andReturn('default.jpg');
        $mockDisk->shouldReceive('path')->once()->with($recipe->image)->andReturn($recipe->image);

        $response = $this->service->getRecipeImage($recipe);
        $this->assertEquals($response, $recipe->image);
    }

    public function testGetRecipeImageWithNoImage(): void
    {
        Storage::fake('recipeImages');
        $recipe = Recipe::factory()->create([
            'image' => null
        ]);
        $mockDisk = $this->mock(LocalFilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('recipeImages')->andReturn($mockDisk);

        $mockDisk->shouldReceive('path')->once()->with('default.jpg')->andReturn('default.jpg');

        $response = $this->service->getRecipeImage($recipe);
        $this->assertEquals($response, 'default.jpg');
    }

    public function testGetDirectionImage(): void
    {
        Storage::fake('recipeImages');
        $direction = Direction::factory()->create();
        $mockDisk = $this->mock(LocalFilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('recipeImages')->andReturn($mockDisk);

        $mockDisk->shouldReceive('path')->once()->with($direction->image)->andReturn($direction->image);

        $response = $this->service->getDirectionImage($direction);
        $this->assertEquals($response, $direction->image);
    }

    public function testGetDirectionImageWithNoImage(): void
    {
        Storage::fake('recipeImages');
        $direction = Direction::factory()->create([
            'image' => null
        ]);

        $response = $this->service->getDirectionImage($direction);

        $this->assertNull($response);
    }
}
