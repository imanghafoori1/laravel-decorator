<?php

use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Imanghafoori\Decorator\Decorators\DecoratorFactory;

class CacheResultDecoratorTest extends TestCase
{
    public function testCacheResultDecorator()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', DecoratorFactory::cache('hello', 2));

        App::shouldReceive('call')->once()->andReturn('We may never know?!');

        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));

        \MyFacade::forgetDecorations('getGiven');
    }

    public function testPermanentCacheResultDecorator()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', DecoratorFactory::foreverCache(function ($a) {
            return 'cache_key_'.$a;
        }));

        App::shouldReceive('call')->twice()->andReturn('We may never know?!');

        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));

        \MyFacade::forgetDecorations('getGiven');
    }
}
