<?php

namespace Imanghafoori\Decorator\Facade;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Decorator extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return \Imanghafoori\Decorator\Decorator::class;
    }
}
