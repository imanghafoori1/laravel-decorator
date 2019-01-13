<?php

namespace Imanghafoori\Decorator\Decorators;

class CacheResults
{
    public static function cache($key, $minutes = 1)
    {
        return self::getDecoratorFactory($key, 'remember', $minutes);
    }

    public static function foreverCache($key)
    {
        return self::getDecoratorFactory($key, 'rememberForever');
    }

    /**
     * @param $key
     * @param $minutes
     * @param $remember
     *
     * @return \Closure
     */
    private static function getDecoratorFactory($key, $remember, $minutes = null): \Closure
    {
        return function ($callable) use ($key, $minutes, $remember) {
            return function (...$params) use ($callable, $key, $minutes, $remember) {
                $cb = function () use ($callable, $params) {
                    return \App::call($callable, $params);
                };

                return cache()->$remember(...array_filter([$key, $minutes, $cb], function ($el) {
                    return !is_null($el);
                }));
            };
        };
    }
}
