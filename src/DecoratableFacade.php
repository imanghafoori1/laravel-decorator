<?php

namespace Imanghafoori\Decorator;

use Illuminate\Support\Facades\Facade;

class DecoratableFacade extends Facade
{
    /**
     * The decorators for resolved object instances.
     *
     * @var array
     */
    protected static $decorations = [];

    protected static $classDecorations = [];

    public static function decorateMethod(string $method, $decorator)
    {
        static::$decorations[$method][] = $decorator;
    }

    public static function decorateClass($decorator)
    {
        static::$classDecorations[] = $decorator;
    }

    public static function getDecorations($method)
    {
        return static::$decorations[$method] ?? [];
    }

    public static function forgetDecorations($method = null)
    {
        if ($method) {
            unset(static::$decorations[$method]);
        } elseif (is_null()) {
            static::$decorations = [];
        }
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $callback = function (...$args) use ($method) {
            return parent::__callStatic($method, $args);
        };

        $decorators = self::getDecorations($method) + static::$classDecorations;
        $callback = app(Decorator::class)->getDecoratedCall($callback, $decorators);

        return $callback(...$args);
    }
}