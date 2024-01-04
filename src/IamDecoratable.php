<?php

namespace Imanghafoori\Decorator;

trait IamDecoratable
{
    /**
     * The decorators for resolved object instances of the facade.
     *
     * @var array
     */
    protected static $decorations = [];

    /**
     * The decorators for all the methods.
     *
     * @var array
     */
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
            static::$classDecorations = [];
        }
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
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
        $callback = app(Decorator::class)->decorateWith($callback, $decorators);

        return $callback(...$args);
    }
}
