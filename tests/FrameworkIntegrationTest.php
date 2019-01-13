<?php

use Imanghafoori\Decorator\Facade\Decorator;

class FrameworkIntegrationTest extends TestCase
{
    public function testDecoratableFacade()
    {
        Decorator::define('stringifyResult', ResultCasterDecorator::class.'@_toString');

        Decorator::decorate(Calculator::class.'@add', 'stringifyResult');
        $result = Decorator::call(Calculator::class.'@add', [-10, -10]);

        $this->assertIsString($result);
        $this->assertEquals('-20', $result);
    }
}