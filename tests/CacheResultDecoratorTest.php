<?php

use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Imanghafoori\Decorator\Decorators\DecoratorFactory;

class CacheResultDecoratorTest extends TestCase
{
    public function testCacheResultDecorator()
    {
        Container::getInstance()->singleton('abc', cachee::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', DecoratorFactory::cache('hello', 2));

        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));

        $this->assertEquals(1, cachee::$counter);
        \MyFacade::forgetDecorations('getGiven');

        // clean up:
        cachee::$counter = 0;
    }

    public function testPermanentCacheResultDecorator()
    {

        Container::getInstance()->singleton('abc', cachee::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', DecoratorFactory::foreverCache(
            fn ($a) => 'cache_key_'.$a)
        );

        cachee::$counter = 0;

        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(2));
        $this->assertEquals('We may never know?!', \MyFacade::getGiven(1));

        $this->assertEquals(2, cachee::$counter);

        \MyFacade::forgetDecorations('getGiven');
    }
}

class cachee
{
    public static $counter = 0;

    public function getGiven()
    {
        self::$counter++;

        return 'We may never know?!';
    }
}
