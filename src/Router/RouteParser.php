<?php

namespace Jtech\Framework\Router;

class RouteParser
{
    public static function getVarsParts(string $path)
    {
        preg_match_all('/{[^}]*}/', $path, $matches);

        return reset($matches) ?? [];
    }
}