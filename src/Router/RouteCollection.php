<?php

namespace Jtech\Framework\Router;

use ArrayAccess;
use Countable;
use Iterator;

final class RouteCollection implements Countable, ArrayAccess, Iterator
{

    /**
     * @var array<string, Route>
     */
    private array $routes;
    /**
     * @var int[]|string[]
     */
    private array $keys;

    private int $position;

    /**
     * @param array<string, Route> $routes
     */
    public function __construct(array $routes = [])
    {
        $this->position = 0;
        $this->routes = $routes;
        $this->keys = array_keys($routes);
    }

    /**
     * @param Route|string $name
     * @param Route|null $value
     * @return $this
     */
    public function set(Route|string $name, ?Route $value): self
    {
        if (is_null($value)){
            if (!$name instanceof Route){
                throw new \TypeError(
                    sprintf('The $name parameter must be of type %s if the $value parameter is null.', Route::class)
                );
            }
            $this->offsetSet($name->getName(), $name);
            return $this;
        }
        if (is_string($name) && !$value instanceof Route){
            throw new \TypeError(
                sprintf('The $value parameter cannot be null if the $name parameter is a string. Replace it with an instance of %s.', Route::class)
            );
        }

        $this->offsetSet($name, $value);

        return $this;
    }

    /**
     * @param $name
     * @return ?Route
     */
    public function get($name): ?Route
    {
        return $this->offsetGet($name);
    }

    /**
     * @param string|Route $route
     * @return RouteCollection
     */
    public function remove(Route|string $route): self
    {
        $name = $route instanceof Route ? $route->getName() : $route;

        $this->offsetUnset($name);
        return $this;

    }

    public function all(bool $withKeys = false): array
    {
        if ($withKeys) {
            return $this->routes;
        }

        return array_values($this->routes);
    }

    public function count(): int
    {
        return count($this->routes);
    }

    /**
     * @inheritDoc
     * @param string|Route $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        $name = $offset instanceof Route ? $offset->getName() : $offset;
        return isset($this->routes[$name]);
    }

    /**
     * @inheritDoc
     * @return ?Route
     */
    public function offsetGet(mixed $offset): ?Route
    {
        return $this->routes[$offset] ?? null;
    }

    /**
     * @inheritDoc
     * @param string|Route $offset
     * @param ?Route $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset instanceof Route){
            if (false === is_null($value)){
                throw new \TypeError(
                    sprintf('The $value parameter must be null when the $offset parameter is an instance of %s.', Route::class)
                );
            }
            if ($this->offsetExists($offset)){
                throw new \LogicException(
                    sprintf('The %s route already exists. Change the route name', $offset->getName())
                );
            }
            $this->routes[$offset->getName()] = $offset;
            $this->keys[] = array_key_last($this->routes);
            return;
        }

        if (is_null($offset) && is_null($value)){
            throw new \TypeError(
                'Unable to register this route. Both parameters cannot be of type null'
            );
        }

        if (is_null($offset)){
            throw new \TypeError(
                'The offset parameter cannot be null'
            );
        }

        if (!$value instanceof Route && !is_string($offset)){
            throw new \TypeError(
                sprintf('The $offset parameter must be a string and the $value parameter an instance of %s.', Route::class)
            );
        }


        $this->routes[$offset] = $value;
        $this->keys[] = array_key_last($this->routes);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->routes[$offset]);
        unset($this->keys[array_search($offset, $this->keys)]);

        $this->keys = array_values($this->keys);
    }

    /**
     * @inheritDoc
     */
    public function current(): Route
    {
        return $this->routes[$this->keys[$this->position]];
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function key(): mixed
    {
        return $this->keys[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->keys[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
}