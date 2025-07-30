<?php

namespace Tests\Feature\Controllers\API;

use App\Models\Direction;
use App\Models\Recipe;
use App\Services\DirectionService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DirectionControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
        $this->service = $this->mock(DirectionService::class);
    }

    public function testStore()
    {
        $recipe = Recipe::factory()->for($this->user->userGroup)->create();
        $this->service->shouldReceive('createDirection')->once()->andReturn(Direction::factory()->create());
        $response = $this->post(route('api.directions.store', ['recipe' => $recipe->getKey()]), [
            'content' => $this->faker->sentence(),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ]);
        $response->assertCreated();
    }

    public function testUpdate()
    {
        $direction = Direction::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->create();
        $this->service->shouldReceive('updateDirection')->once()->andReturn($direction);
        $response = $this->put(route('api.directions.update', ['recipe' => $direction->recipe->getKey(), 'direction' => $direction->getKey()]), [
            'content' => $this->faker->sentence(),
            'image' => UploadedFile::fake()->image('image.jpg'),
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateOrders()
    {
        $directions = Direction::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->count(3)->create();
        $this->service->shouldReceive('updateDirectionOrder')->times(3);
        $response = $this->put(route('api.directions.update-orders', ['recipe' => $directions[0]->recipe->getKey()]), [
            'directions' => [
                ['id' => $directions[0]->getKey(), 'order' => 1],
                ['id' => $directions[1]->getKey(), 'order' => 2],
                ['id' => $directions[2]->getKey(), 'order' => 3],
            ],
        ]);
        $response->assertNoContent();
    }

    public function testDelete()
    {
        $direction = Direction::factory()->for(
            Recipe::factory()->for($this->user->userGroup)->create()
        )->create();
        $this->service->shouldReceive('deleteDirection')->once();
        $response = $this->delete(route('api.directions.delete', ['recipe' => $direction->recipe->getKey(), 'direction' => $direction->getKey()]));
        $response->assertNoContent();
    }
}
