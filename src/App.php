<?php

namespace Jtech\Framework;

use GuzzleHttp\Psr7\ServerRequest;
use Jtech\Framework\Http\Response;
use Jtech\Framework\Router\Route;
use Jtech\Framework\Router\Router;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;

class App
{
    /**
     * @var array
     */
    private $context;
    private  $routes;

    public function __construct(array $context = [])
    {
        $this->context = $context;

        $routes = Yaml::parseFile($context['project_dir'] . '/config/routes.yaml');
        $this->routes = $this->loadRoutes($routes['routes']);
    }

    /**
     * @return array<int, RequestInterface & Response>
     */
    public function start(): array
    {
        $request = ServerRequest::fromGlobals();
        $response = new Response();

        $match = $this->routes->match($request);
        switch ($match['status']) {
            case 0:
                $response = $response->setContent('Page not found!')
                    ->setStatus(404);
                break;
            case 1:
                $response = $response->setStatus(405)
                    ->setContent('Method not allowed');
                break;
            case 2:
                /** @var Route $route */
                $route = $match['route'];
                if ($route->hasParams()){
                    foreach ($route->getParams() as $key => $param) {
                        $request = $request->withAttribute($key, $param);
                    }
                }
                $response = $response->setContent('<h1>Page found</h1>');
                break;
            default:
                break;
        }

        return [$request, $response];
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface|Response $response
     * @return int
     */
    public function run(RequestInterface $request, ResponseInterface|Response $response): int
    {
        if ($response instanceof Response){
            $response->send();
        }

        return $response->getStatusCode();
    }

    private function loadRoutes(array $routes): Router
    {
        $router = new Router(new \AltoRouter());
        foreach ($routes as $key => $route) {
            $route = (object)$route;
            $methods = is_array($route->method) ? $route->method : [$route->method];
            $router->add($route->path, $methods, $route->controller, $route->name);
        }
        return $router;
    }
}