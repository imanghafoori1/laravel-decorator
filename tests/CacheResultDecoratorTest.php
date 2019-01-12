<?php

use Illuminate\Support\Facades\App;
use Imanghafoori\Decorator\Decorators\CacheResults;

class CacheResultDecoratorTest extends TestCase
{
    public function testCacheResultDecorator()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', CacheResults::cache('hello'));

        App::shouldReceive('call')->once()->andReturn('We may never know?!');

        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));

        \MyFacade::forgetDecorations('getGiven');
    }
}