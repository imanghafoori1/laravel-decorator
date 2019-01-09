## Laravel Decorator

### Easily decorate your method calls with laravel-decorator package

#### This is a try to port python decorators into laravel framework.


### Installation :

```
composer require imanghafoori/laravel-decorator
```

### Usage :


### What is a "Decorator" ?

A `decorator` :

1 - Is a callable
2 - That takes a callable (as it's only argument)
3 - and returns a callable

### Sample decorators:

1 - **static method**

Here we just cast the result of the original call to an string.


![image](https://user-images.githubusercontent.com/6961695/50929036-81059f00-1471-11e9-9734-90b226501ed9.png)

**Note:** that finally we return a closure (not the strinfified result of call)
