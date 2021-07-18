<?php

namespace Imanghafoori\Decorator\Facade;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * Class Decorator.
 *
 * @method static define($name, $callback)
 * @method static decorate($callback, $decorator)
 * @method static call($callback, array $parameters = [], $defaultMethod = null)
 * @method static unDecorate($decorated, $decorator = null)
 */
class Decorator extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return 'decorator';
    }
}
