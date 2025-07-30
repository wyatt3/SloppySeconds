<?php

namespace Tests\Feature\Controllers\API;

use App\Models\Direction;
use App\Models\Recipe;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    private $service;
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
        $this->service = $this->mock(ImageService::class);
    }

    public function testGetRecipeImage()
    {
        $recipe = Recipe::factory()->for($this->user->userGroup)->create();
        $this->service->shouldReceive('getRecipeImage')->once()->andReturn(UploadedFile::fake()->image('image.jpg'));
        $response = $this->get(route('recipe.image', ['recipe' => $recipe->getKey()]));
        $response->assertSuccessful();
    }

    public function testGetDirectionImage()
    {
        $direction = Direction::factory()
            ->for(
                Recipe::factory()
                    ->for($this->user->userGroup)
                    ->create()
            )->create();
        $this->service->shouldReceive('getDirectionImage')->once()->andReturn(UploadedFile::fake()->image('image.jpg'));
        $response = $this->get(route('direction.image', ['recipe' => $direction->recipe->getKey(), 'direction' => $direction->getKey()]));
        $response->assertSuccessful();
    }

    public function testGetDirectionImageHandlesNoImage()
    {
        $direction = Direction::factory()
            ->for(
                Recipe::factory()
                    ->for($this->user->userGroup)
                    ->create()
            )->create();
        $this->service->shouldReceive('getDirectionImage')->once()->andReturn(null);
        $response = $this->get(route('direction.image', ['recipe' => $direction->recipe->getKey(), 'direction' => $direction->getKey()]));
        $response->assertNotFound();
    }
}
