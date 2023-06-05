<?php declare(strict_types=1);

namespace Jtech\Framework\Router;

use Psr\Http\Message\RequestInterface;

class Router
{

    use RouterTrait;

    private RouteCollection $routes;
    private \AltoRouter $altoRouter;

    public function __construct(\AltoRouter $altoRouter)
    {
        $this->routes = new RouteCollection();
        $this->altoRouter = $altoRouter;
    }

    public function addRoute(Route $route): static
    {
        $this->routes->set($route->getName(), $route);
//        dump($this->routes);
        return $this;
    }

    /**
     * @param $path
     * @param $method
     * @param $controller
     * @param $name
     * @return $this
     */
    public function add($path, $method, $controller, $name): static
    {
        $routeData = $this->parseRoute($path);

        $route = new Route($routeData, $method, $controller, $name);

        return $this->addRoute($route);

    }

    public function getRoute(string $name): ?Route
    {
        return $this->routes->get($name);
    }

    /**
     * @param RequestInterface $request
     * @return array<string, int|Route|null>
     */
    public function match(RequestInterface $request): array
    {

        $path = $request->getRequestTarget();
        $match = [];
        $route = null;

        /**
         * @var string $key
         * @var  Route $route
         */
        foreach ($this->routes as $key => $item) {
            if (preg_match($item->getPath(), $path)) {
                $route = $item;
                break;
            }
        }

        if ($route){
            if (!in_array($request->getMethod(), $route->getMethod())){
                $match = [
                    'status' => 1,
                ];
            }else{
                $match = [
                    'status' => 2
                ];
                if ($route->hasParams()){
                    preg_match($route->getPath(), $path, $params);
                    foreach (array_keys($route->getParams()) as $paramName) {
                        $param = preg_match('/^\d+$/', $params[$paramName]) ? (int)$params[$paramName] : $params[$paramName];
                        $route->setParam($paramName, $param);
                    }
                }
            }
        }else{
            $match = [
                'status' => 0,
            ];
        }

        $match['route'] = $route;

        return $match;
    }

    private function parseRoute($path): array
    {
        $path = $path === '/' ? $path : rtrim($path, '/');

        $routeDatas = [];
        $regex = $path;
        $varsParts = RouteParser::getVarsParts($path);
        foreach ($varsParts as $part) {
            $var = trim($part, '{\}');
            $varDetails = explode(':', $var);
            if (count($varDetails) > 2){
                dd('Bad param config');
            }

            $varName = $varDetails[0];
            $varRegex = $varDetails[1] ?? '.*';
            $regex = str_replace($part, '(?P<' . $varName . '>'.$varRegex.')', $regex);
            $routeDatas['params'][$varName] = $varRegex;
        }

        $routeDatas += [
            'regex' => '#^' . $regex . '$#',
            'path' => $path
        ];
        return $routeDatas;
    }


}