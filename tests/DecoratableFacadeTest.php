<?php

use Illuminate\Container\Container;
use Imanghafoori\Decorator\Decorator;
use Imanghafoori\Decorator\IamDecoratable;

class DecoratableFacadeTest extends TestCase
{
    public function testDecoratableFacade()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::forgetDecorations('getGiven');
        \MyFacade::decorateMethod('getGiven', function ($f) {
            return function () {
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
        \MyFacade::forgetDecorations();
    }

    public function testDecoratableFacade2()
    {
        Container::getInstance()->singleton('abc', abc::class);
        Container::getInstance()->make(Decorator::class)->define('stringifyResult', [ResultCasterDecorator::class, 'toStringStaticDecorator']);

        \MyFacade::forgetDecorations();
        \MyFacade::decorateMethod('getGiven', 'stringifyResult');

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testStaticMethodsAsDecoratorsOnFacades()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::decorateMethod('getGiven', ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertIsString(\MyFacade::getGiven(1));
        $this->assertEquals('2', \MyFacade::getGiven(2));
        \MyFacade::forgetDecorations('getGiven');
    }

    public function testFacadeClassDecorators()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::decorateAll(ResultCasterDecorator::class.'@minimumParamZero');
        \MyFacade::decorateAll(ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertEquals('1', \MyFacade::getGiven(1));
        $this->assertEquals('0', \MyFacade::getGiven(-2));
        $this->assertIsString(\MyFacade::getGiven(-11));

        \MyFacade::forgetDecorations('getGiven');
    }

    public function testFacadeClassDecoratorsCombination()
    {
        Container::getInstance()->singleton('abc', abc::class);
        \MyFacade::decorateMethod('getGiven', ResultCasterDecorator::class.'@minimumParamZero');
        \MyFacade::decorateAll(ResultCasterDecorator::class.'@toStringDecorator');

        $this->assertEquals('1', \MyFacade::getGiven(1));
        $this->assertEquals('0', \MyFacade::getGiven(-2));
        $this->assertIsString(\MyFacade::getGiven(-2));
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

class MyFacadeWithTrait extends \Illuminate\Support\Facades\Facade
{
    use IamDecoratable;

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
