<?php

namespace Imanghafoori\Decorator;

use Illuminate\Support\ServiceProvider;

class DecoratorServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Decorator::class, Decorator::class);
        $this->app->singleton('decorator', Decorator::class);
    }

    public function provides()
    {
        return [Decorator::class];
    }
}