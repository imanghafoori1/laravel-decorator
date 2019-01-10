# Laravel Decorator

## Easily decorate your method calls with laravel-decorator package

### This is a try to port python decorators into laravel framework.



### Usage Example:

Imagine that you want to put a cache layer between a `MadRepository` and a `MadController`.

But they are both so mad, that they do not allow you to touch a single line of their code.

It smells like `open-closed principle` yeah ?! 👃 

**(Probably both classes are imprisoned in the `vendor` folder and are part of a laravel package, so you can not touch them)**

```php
class MadController extends Controller
{
    public function index () {
        $mads = MadRepositoryFacade::getAllMads();
        ...
    }
}
```

So, what now ?!
Then you can go to `AppServiceProvider.php` (without any mad person realizing it.) 😁 

```php
public function boot( ) {
    
    MadRepositoryFacade::decorate('getAllMads', '\App\Decorators@cache', ['myMadKey', 10]);
}
```
Just that. You will get cached results in your controller, then !


### Installation :

```
composer require imanghafoori/laravel-decorator
```

### What is a "Decorator" ?

A `"Decorator"` :

1 - Is a "callable"

2 - It takes a "callable" (as it's only argument)

3 - It returns a "callable"


### Sample decorators:

For good working examples please take a look at the tests folder.

1 - **static method** 

Here you can see the bare anatomy of a decorator, it just casts the result of the original callable to an string.

![image](https://user-images.githubusercontent.com/6961695/50929036-81059f00-1471-11e9-9734-90b226501ed9.png)

**Note:** that finally we return a closure (not the stringified result of call)

### How to decorate a method on some class ?

```php

\Decorator::decorate('class@method, 'someClass@someDecoratorMethod');

// You may set multiple decorators on a single method...

\Decorator::decorate('class@method, 'someClass@someOtherDecorator');
```


### How to call a method with it's decorator ?

```php
$result = app('decorator')->call('class@method, ...
```

Or you may use the Decorator Facade:

```php
$result = \Decorator::call('myClass@myMethod', ...
```



## Decorate Facades :

### How to decorate Facade methods ?

First, you should extend the `Imanghafoori\Decorator\DecoratableFacade` class (instead of the laravel base Facade).

![image](https://user-images.githubusercontent.com/6961695/50964214-e85c3700-14e3-11e9-8153-71d424daedad.png)


### How to decorate Facade methods:

Like this :

![image](https://user-images.githubusercontent.com/6961695/50963877-1f7e1880-14e3-11e9-9c5e-90d23d1533d5.png)


then if you call your facade as normal you get decorated results.

![image](https://user-images.githubusercontent.com/6961695/50963709-b0082900-14e2-11e9-84c8-c1665693d390.png)

