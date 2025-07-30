<?php

namespace Tests\Feature\Controllers\API;

use App\Enums\RecipeType;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->partialMockWithConstructor(RecipeService::class);
        $this->actingAs($this->user);
    }

    public function testIndex()
    {
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);

        $response = $this->get(route('api.recipes.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment($recipe->toArray());
    }

    public function testShow()
    {
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);

        $response = $this->get(route('api.recipes.show', ['recipe' => $recipe->getKey()]));

        $response->assertStatus(200);
        $response->assertJsonFragment($recipe->toArray());
    }

    public function testStore()
    {
        $this->service->shouldReceive('createRecipe')->once()->andReturn(Recipe::factory()->create());
        $response = $this->post(route('api.recipes.store'), [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'image' => UploadedFile::fake()->image('image.jpg'),
            'type' => $this->faker->randomElement(RecipeType::cases())->name
        ]);

        $response->assertStatus(201);
    }

    public function testUpdate()
    {
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);
        $this->service->shouldReceive('updateRecipe')->once()->andReturn($recipe);

        $response = $this->put(route('api.recipes.update', ['recipe' => $recipe->getKey()]), [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'image' => UploadedFile::fake()->image('image.jpg'),
            'type' => $this->faker->randomElement(RecipeType::cases())->name
        ]);

        $response->assertStatus(200);
    }

    public function testDelete()
    {
        $this->service->shouldReceive('deleteRecipe')->once();
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);

        $response = $this->delete(route('api.recipes.delete', ['recipe' => $recipe->getKey()]));

        $response->assertStatus(204);
    }
}
