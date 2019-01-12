<?php

use Imanghafoori\Decorator\Decorator;

class DecoratableFacadeTest extends TestCase
{
    public function testDecoratableFacade()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', function ($f) {
            return function () {
                return null;
            };
        });

        $this->assertNull(\MyFacade::getGiven(1));
        $this->assertNull(\MyFacade::getGiven(2));

        \MyFacade::decorateMethod('getGiven', function ($f) {
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
        app(Decorator::class)->define('stringifyResult', [ResultCasterDecorator::class, 'toStringStaticDecorator']);

        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', 'stringifyResult');

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testStaticMethodsAsDecoratorsOnFacades()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::decorateMethod('getGiven', ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testFacadeClassDecorators()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::decorateClass(ResultCasterDecorator::class.'@minimumParamZero');
        \MyFacade::decorateClass(ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertEquals('1', \MyFacade::getGiven(1));
        $this->assertEquals('0', \MyFacade::getGiven(-2));
        $this->assertIsString(\MyFacade::getGiven(-11));

        \MyFacade::forgetDecorations('getGiven');
    }

    public function testFacadeClassDecoratorsCombination()
    {
        app()->singleton('abc', abc::class);
        \MyFacade::decorateMethod('getGiven', ResultCasterDecorator::class.'@minimumParamZero');
        \MyFacade::decorateClass(ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertEquals('1', \MyFacade::getGiven(1));
        $this->assertEquals('0', \MyFacade::getGiven(-2));
        $this->assertIsString(\MyFacade::getGiven(-11));

        \MyFacade::forgetDecorations('getGiven');
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