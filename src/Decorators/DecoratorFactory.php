<?php

namespace Imanghafoori\Decorator\Decorators;

use Closure;
use Illuminate\Container\Container;

class DecoratorFactory
{
    public static function cache($key, $minutes = 1)
    {
        return self::getDecoratorFactory($key, 'remember', $minutes);
    }

    public static function foreverCache($key)
    {
        return self::getDecoratorFactory($key, 'rememberForever');
    }

    public static function variadicParam()
    {
        return function ($callable) {
            return function (...$param) use ($callable) {
                $param = is_array($param[0]) ? $param[0] : $param;

                return Container::getInstance()->call($callable, $param);
            };
        };
    }

    /**
     * @param  $key
     * @param  $minutes
     * @param  $remember
     * @return \Closure
     */
    private static function getDecoratorFactory($key, $remember, $minutes = null): Closure
    {
        return function ($callable) use ($key, $minutes, $remember) {
            return function (...$params) use ($callable, $key, $minutes, $remember) {
                $cb = fn () => Container::getInstance()->call($callable, $params);

                if (is_callable($key)) {
                    $key = $key(...$params);
                }

                return Container::getInstance()->make('cache')->$remember(...array_filter([$key, $minutes, $cb], fn ($el) => ! is_null($el)));
            };
        };
    }
}
