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
    
    MadRepositoryFacade::decorateMethod('getAllMads', '\App\Decorators@cache', ['myMadKey', 10]);
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

1 - **static method** 

Here you can see the bare anatomy of a decorator, it just casts the result of the original callable to an string.

![image](https://user-images.githubusercontent.com/6961695/50929036-81059f00-1471-11e9-9734-90b226501ed9.png)

**Note:** that finally we return a closure (not the stringified result of call)

2 - **Closure**



### How to call a method with it's decorator ?

```php
app('decorator')->call('class@method, ...
```

## Decorate Facades :

### How to make a Facade decoratable ?

If you extend the `Imanghafoori\Decorator\DecoratableFacade` class (instead of the laravel base Facade) in your facade, all the method calls on your facade become "decoratable" and you will be able to decorate them.

### How to decorate Facade methods:

You can do so by calling the static `decorate` method on the facade class.

![image](https://user-images.githubusercontent.com/6961695/50934957-8d90f400-147f-11e9-8e70-f3ee6edd4bc6.png)



