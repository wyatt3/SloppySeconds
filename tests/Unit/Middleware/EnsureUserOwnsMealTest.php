<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureUserOwnsMeal;
use App\Models\Meal;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class EnsureUserOwnsMealTest extends TestCase
{
    private $middleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureUserOwnsMeal();
    }

    public function testHandle()
    {
        $meal = Meal::factory()->create([
            'user_group_id' => $this->user->user_group_id
        ]);

        $mockRequest = $this->mock(Request::class, function ($mock) use ($meal) {
            $mock->shouldReceive('user')->once()->andReturn($this->user);
            $mock->shouldReceive('route')->once()->andReturn($meal);
        });

        $this->middleware->handle($mockRequest, function () {
            $this->assertTrue(true);
            return response("", 200);
        });
    }

    public function testHandleAborts()
    {
        $this->expectException(NotFoundHttpException::class);

        $meal = Meal::factory()->create();
        $mockRequest = $this->mock(Request::class, function ($mock) use ($meal) {
            $mock->shouldReceive('user')->once()->andReturn($this->user);
            $mock->shouldReceive('route')->once()->andReturn($meal);
        });

        $this->middleware->handle($mockRequest, function () {
            $this->fail('Middleware should have aborted with a 404');
        });
    }
}
