<?php

abstract class TestCase extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [\Imanghafoori\Decorator\DecoratorServiceProvider::class];
    }
}
