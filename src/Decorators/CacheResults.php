<?php

namespace Imanghafoori\Decorator\Decorators;

class CacheResults
{
    public static function cache($key, $minutes = 1)
    {
        return self::getDecoratorFactory($key, $minutes, 'remember');
    }

    public static function permanentCache($key, $minutes = 1)
    {
        return self::getDecoratorFactory($key, $minutes, 'rememberForever');
    }

    /**
     * @param $key
     * @param $minutes
     * @param $remember
     * @return \Closure
     */
    private static function getDecoratorFactory($key, $minutes, $remember): \Closure
    {
        return function ($callable) use ($key, $minutes, $remember) {
            return function (...$params) use ($callable, $key, $minutes, $remember) {
                return cache()->$remember($key, $minutes, function () use ($callable, $params) {
                    return \App::call($callable, $params);
                });
            };
        };
    }
}