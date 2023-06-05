<?php

namespace Jtech\Framework\Router;


/**
 * @method self add(string $path, array $method, string|callable $controller, string $name)
 */
trait RouterTrait
{


    public function get($path, $controller, $name): static
    {
        return $this->add(
            $path,
            ['GET'],
            $controller,
            $name
        );
    }

    public function post($path, $controller, $name): static
    {
        return $this->add(
            $path,
            ['POST'],
            $controller,
            $name
        );
    }

    public function put($path, $controller, $name): static
    {
        return $this->add(
            $path,
            ['PUT'],
            $controller,
            $name
        );
    }

    public function patch($path, $controller, $name): static
    {
        return $this->add(
            $path,
            ['PATCH'],
            $controller,
            $name
        );
    }


}