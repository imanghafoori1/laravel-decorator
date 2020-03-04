# :christmas_tree: Laravel Decorator 

### Decorator pattern in laravel apps

[![Build Status](https://travis-ci.org/imanghafoori1/laravel-decorator.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-decorator)
<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-decorator"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-decorator.svg?style=round-square" alt="Quality Score"></img></a>
[![StyleCI](https://github.styleci.io/repos/164699371/shield?branch=master)](https://github.styleci.io/repos/164699371)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=round-square)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/laravel-decorator/v/stable)](https://packagist.org/packages/imanghafoori/laravel-decorator)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-decorator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-decorator/?branch=master)
[![Total Downloads](https://poser.pugx.org/imanghafoori/laravel-decorator/downloads)](https://packagist.org/packages/imanghafoori/laravel-decorator)


**Made with :heart: for smart clean coders**


### A try to port "decorator" feature from python language to laravel framework.



![python-and-prey](https://user-images.githubusercontent.com/6961695/51078481-a2ad9300-16ca-11e9-8bf2-1d4ed214e030.jpg)


### :truck: Installation :

```
composer require imanghafoori/laravel-decorator
```



### What is a `"Decorator"` :question:

A decorator is callable which wraps around the original decorated callable, in order to form a new callable composed of the previous two.

Like a python snake swallowing a deer whole and wraps around it's body !

After that the snake becomes capable to eat and digest grasses :herb: because it has a deer inside it.

Technically, A `"Decorator"` :

1 - Is a "callable"

2 - which takes an other "callable" (as it's only argument, like a snake swallows an other snake)

3 - and returns a new `"callable"` (which internally calls the original `callable`, putting some code before and after it.)

**What ?!??! ???!** (0_o)

#### What can be considered as a "`callable`" within laravel ?!

Long story short, anything that can be called (invoked) with `App::call();` or `call_user_func()`
like: `'MyClass@myMethod`' or a closure, `[UserRepo::class, 'find']`

### Cache Like a Pro:

Caching DB queries is always a need,

but it is always annoying to add more code to the existing code.
It will become more messy, we may break the current code, after all it adds a layer of fog. Yeah ?


Imagine that you have a `UserController`which calls a `UserRepo@find` to get a `$user` .

Then after a while you decide to put a cache layer between those two classes for obvious reasons.

According to SOLID principles, you shouldn't put the caching code logic neither in your controller nor your UserRepo.
But somewhere in between.

In other words, you want to add a new feature (caching in this case) without modifing the existing code.

It smells like `Open-closed Principle` Yeah ?! 👃 

You want to keep the responsibilities seperate. In this case `caching` should not be in a repository or controller but in it's own class. 

It smells like `Single Responsibility Principle` yeah ?! 👃 

```php

class UserRepository
{
    function find($uid) {
        return User::find($uid);
    }
}

class MadUsersController extends Controller
{
    function show ($madUserId) {
        $madUser = app()->call('UserRepository@find', ['id' => $madUserId]);
    }
}

```
ok now there is no cache, going on. it is a direct call.

With the help of laravel-decorator built-in cache decorator, you can go to `AppServiceProvider.php` or any other service provider 

```php
<?php

use Imanghafoori\Decorator\Decorators\DecoratorFactory;

class AppServiceProvider extends ServiceProvider {

    public function boot( ) {

        $keyMaker = function ($madId) {
            return 'mad_user_key_' . $madId;
        };
        $time = 10;
        $decorator = DecoratorFactory::cache($keyMaker, $time);
        
        \Decorator::decorate('UserRepository@find, $decorator);
    }
}

```

You will get cached results from your calls, in your `UserController` without touching it !
but rememnber to change :

```php
 app()->call('UserRepository@find', ...
 // to :
  app('decorator')->call('UserRepository@find', ...
```

### Define Your Own Decorators:

```php

public function boot () {
    \Decorator::define('myDecoratorName1', 'SomeClass@someMethod');
    
// or

    \Decorator::define('myDecoratorName2', function ($callable) {
        return function (...) use ($callable){ ... } 
    });
}

```
Then you can use this name (`myDecoratorName`) to decorate methods.


### How to decorate a method ?

```php

// You may set multiple decorators on a single method... 
\Decorator::decorate('class@method, 'someClass@someOtherDecorator'); // (first)

// or reference the decorator by it's name :
\Decorator::decorate('class@method, 'myDecoratorName'); // (second)

```


### How to call a method with it's decorators ?

![image](https://user-images.githubusercontent.com/6961695/51078628-970f9b80-16cd-11e9-8b23-267b2d1564e7.png)


## Decorate Facades :

### Decorating Facade Methods

First, you should extend the `Imanghafoori\Decorator\DecoratableFacade` class (instead of the laravel base Facade).

![image](https://user-images.githubusercontent.com/6961695/51075625-484d0c00-16a3-11e9-9551-73b199a9c5e9.png)


#### Now You Can Apply Decorators in your ServiceProvider's boot method:

![image](https://user-images.githubusercontent.com/6961695/51078788-6715c780-16d0-11e9-91af-710fc9cd51b7.png)


then if you call your facade as normal you get decorated results.


![image](https://user-images.githubusercontent.com/6961695/51075751-3d937680-16a5-11e9-855b-2b8b61bdb876.png)


### :warning: Warning :

With great power, comes great responsibilities.

Remember not to violate the `Liskoves Substitution Principle` when you decorate something.

For example a method call which returns `int|null` should not unexpectedly return a `string` after being decorated.

`$result = app('decorate')->call(...`

Since the users of the method should be ready for type of value they get back.

But if you return only `int` and your decorator causes the `null` value to be filtered out. that's ok.


### :star: Your Stars Make Us Do More :star:

As always if you found this package useful and you want to encourage us to maintain and work on it, Please `press the star button` to declare your willing.


### More packages from the author:

:gem: A minimal yet powerful package to give you opportunity to refactor your controllers.

- https://github.com/imanghafoori1/laravel-terminator

-------------

:gem: A minimal yet powerful package to give a better structure and caching opportunity for your laravel apps.

- https://github.com/imanghafoori1/laravel-widgetize

------------

:gem: It allows you login with any password in local environment only.

- https://github.com/imanghafoori1/laravel-anypass

------------

:gem: Authorization and ACL is now very easy with hey-man package !!!

- https://github.com/imanghafoori1/laravel-heyman

----------------

### 🍌 Reward me a banana 🍌

so that I will have energy to start the next package for you.


- Dodge Coin: DJEZr6GJ4Vx37LGF3zSng711AFZzmJTouN

- LiteCoin: ltc1q82gnjkend684c5hvprg95fnja0ktjdfrhcu4c4

- BitCoin: bc1q53dys3jkv0h4vhl88yqhqzyujvk35x8wad7uf9

- Ripple: rJwrb2v1TR6rAHRWwcYvNZxjDN2bYpYXhZ

- Etherium: 0xa4898246820bbC8f677A97C2B73e6DBB9510151e

--------------
