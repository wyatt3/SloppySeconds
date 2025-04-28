<?php

namespace Tests;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    use WithFaker, DatabaseTransactions;

    /** @var \Illuminate\Contracts\Auth\Authenticatable|User $user */
    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Create a Mockery mock with constructor dependencies.
     *
     * @param  $objOrClass
     * @param Closure|null $mock
     * @return MockInterface
     * @throws BindingResolutionException
     */
    protected function partialMockWithConstructor($objOrClass, ?Closure $mock = null): MockInterface
    {
        $class = new \ReflectionClass($objOrClass);
        $constructor = $class->getConstructor();

        // Get the constructor parameters
        $parameters = [];
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $parameter) {
                $parameterType = $parameter->getType();
                if ($parameterType !== null && !$parameterType->isBuiltin()) {
                    $parameterClassName = $parameterType->getName();
                    $parameterInstance = app()->make($parameterClassName);
                    $parameters[] = $parameterInstance;
                }
            }
        }

        $mock = Mockery::mock($objOrClass, $parameters)->makePartial();

        $this->app->bind($objOrClass, function () use ($mock) {
            return $mock;
        });

        return $mock;
    }

    /**
     * Call an inaccessible method on the given class with the provided arguments
     *
     * @param $objOrClass
     * @param $methodName
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     */
    public static function callInaccessibleMethod($objOrClass, $methodName, array $args)
    {
        $class = new \ReflectionClass($objOrClass);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($objOrClass, $args);
    }

    /**
     * Set an inaccessible property on the given class with the provided value
     *
     * @param $objOrClass
     * @param $propertyName
     * @param $value
     * @return void
     * @throws \ReflectionException
     */
    public static function setInaccessibleProperty($objOrClass, $propertyName, $value)
    {
        $class = new \ReflectionClass($objOrClass);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($objOrClass, $value);
    }

    /**
     * Get an inaccessible property on the given class
     *
     * @param $objOrClass
     * @param $propertyName
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getInaccessibleProperty($objOrClass, $propertyName)
    {
        $class = new \ReflectionClass($objOrClass);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($objOrClass);
    }
}
