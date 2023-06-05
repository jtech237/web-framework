<?php

namespace Jtech\Framework\Router;

class Route
{
    private array $method;

    private string $path;

    /**
     * @var array|callable
     */
    private $controller;

    private string $name;
    private array $params;
    private bool $hasParam;

    /**
     * @param array $pathData
     * @param string[]|string $method
     * @param $controller
     * @param string $name
     * @param array $params
     */
    public function __construct(array $pathData, array|string $method, $controller, string $name)
    {
        if (true === is_string($method)){
            $method = [$method];
        }
        $this->method = array_unique($method);

        $this->path = $pathData['regex'];
        $this->hasParam = isset($pathData['params']);
        $this->controller = $controller;
        $this->name = $name;
        $this->params = $pathData['params'] ?? [];
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @param array|string $method
     * @return Route
     */
    public function setMethod(array|string $method): self
    {
        if (true === is_string($method)){
            $method = [$method];
        }

        $this->method = array_unique($method);

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    public function setPath(string $path):self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array|callable
     */
    public function getController(): callable|array
    {
        return $this->controller;
    }

    /**
     * @param array|callable $controller
     * @return Route
     */
    public function setController(callable|array $controller): Route
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name)
    {
        return $this->params[$name] ?? null;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function hasParams(): bool
    {
        return $this->hasParam;
    }

    public function setParam(string $name, mixed $value): self
    {
        if (!$this->hasParam($name)){
            throw new \LogicException(
                sprintf('The %s route has no %s parameters', $this->name, $name)
            );
        }

        $this->params[$name] = $value;
        return $this;
    }

    private function hasParam(string $name): bool
    {
        return isset($this->params[$name]);
    }

}