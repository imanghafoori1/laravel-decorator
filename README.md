# Laravel Decorator

[![Build Status](https://travis-ci.org/imanghafoori1/laravel-widgetize.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-widgetize)
[![Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/laravel-decorator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-decorator/?branch=master)
[![StyleCI](https://github.styleci.io/repos/164699371/shield?branch=master)](https://github.styleci.io/repos/164699371)

## A try to port "decorators" in python language to laravel framework.


![python-and-prey](https://user-images.githubusercontent.com/6961695/51078481-a2ad9300-16ca-11e9-8bf2-1d4ed214e030.jpg)

#### Why this header is chosen for a laravel package ?! You will shortly see why...

### What is a "Decorator" ?

A decorator wraps around the original function, effectively take over it's behaviour and returns result on it's behalf..

Like a python swallowing a deer and wraps around it...

Decorators take in a callable, wrap around them and return a newly born callable.



### Cache Like a Pro:

Imagine that you want to put a cache layer between a `MadRepository` and a `MadController`.

But they are both so mad, that they do not allow you to touch a single line of their code.

It smells like `open-closed principle` yeah ?! 👃 

**(Probably both `MadRepository` and `MadController` are imprisoned in the `vendor` folder and are part of a laravel package, so you can not touch them)**

```php
class MadController extends Controller
{
    public function index () {
    
        // we don't want to put any cache logic here...
        
        $mads = MadRepositoryFacade::getAllMads();
        ...
    }
}
```

So, what now ?!

With the help of laravel-decorator, you can go to `AppServiceProvider.php` (without any mad person realizing it.) 😁 

```php

use Imanghafoori\Decorator\Decorators\CacheResults;

public function boot( ) {
    
    MadRepositoryFacade::decorateAll('getAllMads', CacheResults::cache('myKey', 10));
}

```
Just that.

You will get cached results from your Facade calls, in your entire app without changing a single line of code !!



### :warning: Warning :

With great power, comes great responsibilities.

Remember not to violate the `Liskoves Substitution Principle` when you decorate something.

For example a function call which returns `int|null` should not unexpectedly return a `string` after being decorated.

Since the users of the function should be ready for type of value they get back from the function call.

But if you return only `int` and your decorator causes the `null` value to be filtered out. that's ok.


### :truck: Installation :

```
composer require imanghafoori/laravel-decorator
```



Technically, A `"Decorator"` :

1 - Is a "callable" (python is an animal)

2 - It takes a "callable" as it's only argument (a python swallow an other animal animal)

3 - It returns a new "callable" (and turns into a bloated animal surrounding it.)

#### Look at the below picture :

Here you can see the bare anatomy of a decorator.

**Why it is a decorator ? well because :**

1 - A public method is a "callable" in php.

2 - The method has taken a "callable" as it's argument.

3 - The whole thing surrounded by the black line is a closure, which is "returned" from the method.


Here we return a callable that calls the original callable and casts it's result into string.

![image](https://user-images.githubusercontent.com/6961695/50966481-4855dc00-14ea-11e9-884f-5e6b762b6e35.png)

**So Let's use this to decorate the baz method on the Bar class :".**

![image](https://user-images.githubusercontent.com/6961695/50967860-a389cd80-14ee-11e9-85a5-e3cf346942a3.png)

Alternatively you can use the `\Decorator` facade to do so



**It's time to enjoy having a decorated call :".**

![image](https://user-images.githubusercontent.com/6961695/50968397-2bbca280-14f0-11e9-85c9-4112e14da056.png)

### Sample decorators:

For good working examples please take a look at the tests folder.

1 - **static method** 

**Note:** that finally we return a closure (not the stringified result of call)

### How to decorate a method on some class ?

```php

// You may set multiple decorators on a single method...

\Decorator::decorate('class@method, 'someClass@someOtherDecorator');
```


### How to call a method with it's decorator ?

```php
$result = app('decorator')->call('class@method, ...
```

![image](https://user-images.githubusercontent.com/6961695/51078628-970f9b80-16cd-11e9-8b23-267b2d1564e7.png)

Or you may use the Decorator Facade:

```php
$result = \Decorator::call('myClass@myMethod', ...
```



## Decorate Facades :

### How to decorate Facade methods ?

First, you should extend the `Imanghafoori\Decorator\DecoratableFacade` class (instead of the laravel base Facade).

![image](https://user-images.githubusercontent.com/6961695/51075625-484d0c00-16a3-11e9-9551-73b199a9c5e9.png)


#### Now You Can Apply Decorators in your ServiceProvider's boot method:


```php

public function boot() {
        
```
![image](https://user-images.githubusercontent.com/6961695/51075654-e93bc700-16a3-11e9-8e5f-f917f2ae6942.png)


```php
}
```

then if you call your facade as normal you get decorated results.


![image](https://user-images.githubusercontent.com/6961695/51075751-3d937680-16a5-11e9-855b-2b8b61bdb876.png)


### How to decorate all methods of a Facade class ?

![image](https://user-images.githubusercontent.com/6961695/51077724-848e6580-16bf-11e9-8cf1-2127271e89dc.png)
