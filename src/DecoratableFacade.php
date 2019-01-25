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

    public static function decorateMethod($method, $decorator)
    {
        foreach ((array) $method as $m) {
            static::$decorations[$m][] = $decorator;
        }
    }

    public static function decorateAll($decorator)
    {
        foreach ((array) $decorator as $d) {
            static::$classDecorations[] = $d;
        }
    }

    public static function getDecorations($method)
    {
        return static::$decorations[$method] ?? [];
    }

    public static function forgetDecorations($method = null)
    {
        if ($method) {
            unset(static::$decorations[$method]);
        } elseif (is_null($method)) {
            static::$decorations = [];
            static::$classDecorationsns = [];
        }
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $callback = function (...$args) use ($method) {
            return parent::__callStatic($method, $args);
        };

        $decorators = self::getDecorations($method) + static::$classDecorations;
        $callback = app(Decorator::class)->decorateWith($callback, $decorators);

        return $callback(...$args);
    }
}
