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

    public static function decorateMethod(string $method, $decorator)
    {
        static::$decorations[$method][] = $decorator;
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
        $callback = function ($method, $args) {
            return parent::__callStatic($method, $args);
        };

        $callback = app(Decorator::class)->getDecoratedCall($callback, self::getDecorations($method));

        return $callback($method, $args);
    }
}