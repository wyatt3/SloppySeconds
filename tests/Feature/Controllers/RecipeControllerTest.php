<?php

namespace Tests\Feature\Controllers;

use App\Models\Recipe;
use App\Services\RecipeService;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->partialMockWithConstructor(RecipeService::class);
    }

    public function testIndex()
    {
        $recipe = Recipe::factory()->create();

        $this->actingAs($this->user);

        $response = $this->get(route('api.recipes.index'));

        $response->assertStatus(200);
    }
}
