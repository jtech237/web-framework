<?php

namespace Test;

use GuzzleHttp\Psr7\Request;
use Jtech\Framework\Router\Route;
use Jtech\Framework\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private $router;

    public function setUp(): void
    {
        $a = $this->createMock(\AltoRouter::class);

        $this->router = new Router($a);
    }

    public function testConstructor()
    {
        $router = $this->router;
        $this->assertInstanceOf(Router::class, $router);
        $this->assertObjectHasProperty('altoRouter', $router);
    }

    public function testSimpleRouteAdd()
    {
        $router = $this->router;
        $routeConfig = [
            'path' => '/',
            'name' => 'home',
            'controller' => 'HomeController::home',
            'method' => 'GET'
        ];
        $router->add($routeConfig['path'], $routeConfig['method'], $routeConfig['controller'], $routeConfig['name']);
        self::assertInstanceOf(Route::class, $router->getRoute($routeConfig['name']));
        self::assertNull($router->getRoute('notExistRoute'));
    }

    public function testRouteWithOneParam()
    {
        $router = $this->router;
        $request = new Request('GET', 'http://localhost/hello/testRouter');
        $routeConfig = [
            'path' => '/hello/{name}',
            'name' => 'home',
            'controller' => 'HomeController::sayHello',
            'method' => 'GET'
        ];
        $router->add($routeConfig['path'], $routeConfig['method'], $routeConfig['controller'], $routeConfig['name']);
        $match = $router->match($request);
        /** @var Route $route */
        $route = $match['route'];
        self::assertInstanceOf(Route::class, $route);
        self::assertTrue($route->hasParams());
        self::assertEquals('testRouter', $route->getParam('name'));

        // For not allowed method
        $request = new Request('POST', 'http://localhost/hello/testRouter');
        $match = $router->match($request);
        /** @var Route $route */
        $route = $match['route'];
        self::assertInstanceOf(Route::class, $route);
        self::assertEquals(1, $match['status']);

    }
}