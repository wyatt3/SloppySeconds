<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureUserOwnsRecipe;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class EnsureUserOwnsRecipeTest extends TestCase
{
    private $middleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureUserOwnsRecipe();
    }

    public function testHandle()
    {
        $recipe = Recipe::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);

        $mockRequest = $this->mock(Request::class, function ($mock) use ($recipe) {
            $mock->shouldReceive('user')->once()->andReturn($this->user);
            $mock->shouldReceive('route')->once()->andReturn($recipe);
        });

        $this->middleware->handle($mockRequest, function () {
            $this->assertTrue(true);
            return response("", 200);
        });
    }

    public function testHandleAborts()
    {
        $this->expectException(NotFoundHttpException::class);

        $recipe = Recipe::factory()->create();
        $mockRequest = $this->mock(Request::class, function ($mock) use ($recipe) {
            $mock->shouldReceive('user')->once()->andReturn($this->user);
            $mock->shouldReceive('route')->once()->andReturn($recipe);
        });

        $this->middleware->handle($mockRequest, function () {
            $this->fail('Middleware should have aborted with a 404');
        });
    }
}
