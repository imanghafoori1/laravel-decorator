<?php

use Imanghafoori\Decorator\Decorator;

class DecoratableFacadeTest extends TestCase
{
    public function testDecoratableFacade()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorate('getGiven', function ($f) {
            return function () {
                return null;
            };
        });

        $this->assertNull(\MyFacade::getGiven(1));
        $this->assertNull(\MyFacade::getGiven(2));

        \MyFacade::decorate('getGiven', function ($f) {
            return function () {
                return 'hello;';
            };
        });

        $this->assertEquals('hello;', \MyFacade::getGiven('hello;'));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testDecoratableFacade2()
    {
        app()->singleton('abc', abc::class);
        app(Decorator::class)->define('stringifyResult', [ResultCasterDecorator::class, 'staticToString']);

        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorate('getGiven', 'stringifyResult');

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testStaticMethodsAsDecoratorsOnFacades()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorate('getGiven', [ResultCasterDecorator::class, 'staticToString']);

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
    }
}

class MyFacade extends \Imanghafoori\Decorator\DecoratableFacade
{
    protected static function getFacadeAccessor()
    {
       return 'abc';
    }
}

class abc
{
    public function getGiven($a)
    {
        return $a;
    }
}