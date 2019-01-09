## Laravel Decorator

### Easily decorate your method calls with laravel-decorator package

#### This is a try to port python decorators into laravel framework.



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

### Usage :


### What is a "Decorator" ?

A `"Decorator"` :

1 - Is a "callable"

2 - That takes a "callable" (as it's only argument)

3 - and returns a "callable"

### Sample decorators:

1 - **static method**

Here we just cast the result of the original call to an string.


![image](https://user-images.githubusercontent.com/6961695/50929036-81059f00-1471-11e9-9734-90b226501ed9.png)

**Note:** that finally we return a closure (not the strinfified result of call)
