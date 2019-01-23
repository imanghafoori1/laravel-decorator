<?php

namespace Imanghafoori\Decorator;

use Illuminate\Support\ServiceProvider;
use Imanghafoori\Decorator\Decorators\DecoratorFactory;

class DecoratorServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(DecoratorFactory::class);
        $this->app->singleton(Decorator::class);
        $this->app->singleton('decorator', Decorator::class);
    }

    public function provides()
    {
        return [Decorator::class, 'decorator', DecoratorFactory::class];
    }
}
