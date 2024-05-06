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
        return fn ($callable) => function (...$param) use ($callable) {
            return Container::getInstance()->call($callable, is_array($param[0]) ? $param[0] : $param);
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
        return fn ($callable) => fn (...$params) => DecoratorFactory::call($callable, $params, $key, $minutes, $remember);
    }

    private static function call($callable, array $params, $key, $minutes, $remember)
    {
        $caller = fn () => Container::getInstance()->call($callable, $params);

        if (is_callable($key)) {
            $key = $key(...$params);
        }

        $args = array_filter([$key, $minutes, $caller], fn ($value) => ! is_null($value));

        return Container::getInstance()->make('cache')->$remember(...$args);
    }
}
