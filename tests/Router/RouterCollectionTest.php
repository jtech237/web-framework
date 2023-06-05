<?php

namespace Test\Router;

use Jtech\Framework\Router\Route;
use Jtech\Framework\Router\RouteCollection;
use PHPUnit\Framework\TestCase;

class RouterCollectionTest extends TestCase
{
    private $collector;

    protected function setUp(): void
    {
        $this->collector = new RouteCollection();
    }

    public function testConstructor()
    {
        self::assertInstanceOf(\ArrayAccess::class, $this->collector);
        self::assertInstanceOf(\Iterator::class, $this->collector);
    }

    public function testSetMethod()
    {
        $route = new Route([
            'regex' => '#^/test$#',
            'path' => '/test',
        ], ['GET'], 'HomeController::home', 'test_route');

        $this->collector->set($route, null);
        self::assertContains($route, $this->collector);
        self::assertArrayHasKey('test_route', $this->collector);
    }

    public function testRemoveMethod()
    {
        $route = $this->createStub(Route::class);
        $route->
        $route = new Route([
            'regex' => '#^/test$#',
            'path' => '/test',
        ], ['GET'], 'HomeController::home', 'test_route');

        $this->collector->set($route, null);
        self::assertContains($route, $this->collector);

        $this->collector->remove($route);
        self::assertNotContains($route, $this->collector);
    }
}