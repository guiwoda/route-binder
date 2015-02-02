# Route Binder
Laravel route binding, done right.

[![Build Status](https://travis-ci.org/guiwoda/route-binder.svg)](https://travis-ci.org/guiwoda/route-binder)

## The problem
Projects start simple: a few routes, maybe some resource controllers, and maybe some parameter binding here and there.
But soon, the `routes.php` file starts to pile up, spawning hundreds of lines, with complex nested groups and filters
or even (god forbid) having calls to `App::make`. Even more cumbersome, having to scroll all those lines searching for
that odd route name that you clearly forget because, who remembers those anyway?

This package helps you with (at least) three things:

1. It makes your routes part of your *Application* by [letting you use DI through the IoC container](#ioc)
2. It lets you split up routes in multiple files (classes) without the need for old-fashioned `includes` or `requires`
3. As you'll be creating classes, you have an opportunity to declare some string constants and hold references to those nasty route names

## The solution
This package is just a contract, a config file and a `ServiceProvider`.

As usual, include the `ServiceProvider` in your `app/config/app.php` file like so:

```php
    'providers' => [
        // ...
        'GuiWoda\RouteBinder\RouteBinderServiceProvider',
        // ...
    ]
```

Then, publish the package's configuration:

```bash
    php artisan vendor:publish
```

Afterwards, you'll need to create some classes that implement the `GuiWoda\RouteBinder\RouteBinder` interface.
Don't panic! You'll see it's a piece of cake:

```php
    namespace App\Http\Routes;

    use GuiWoda\RouteBinder\RouteBinder;
    use Illuminate\Routing\Router;

    class FooRouteBinder implements RouteBinder
    {
        /**
         * This is what I meant with #3 up there.
         * Completely optional, but highly recommended.
         */
        const INDEX = 'foo.index';

        /**
         * The $router instance is the same as what you get
         * when you use the Route facade! No change there ;-)
         */
        public function bind(Router $router)
        {
            $router->get('foo', ['as' => self::INDEX, 'uses' => function(){
                return \View::make('hello');
            }]);
        }
    }
```

And add them to the published config file (you find it now in `app/config/packages/guiwoda/route-binder/routes.php`):

```php
    return [
        App\Http\Routes\FooRouteBinder::class,
        App\Http\Routes\BarRouteBinder::class,
        App\Http\Routes\BazRouteBinder::class,
        App\Http\Routes\AwesomeRouteBinder::class,
    ];
```

And you're done! Now all your routes are nicely organized, and if things get out of hand, you can always split 'em up more!

## <a name="ioc"></a> The IoC Container
I love Laravel's [Route model binding](http://laravel.com/docs/4.2/routing#route-model-binding) functionality. I must
confess tho, I don't use Eloquent, so I always go for the `Route::bind()` option.

But this feature, as powerful as it may be, is pretty nasty on your architecture. Having calls to the DB on the `routes.php` file
is awful, and going `App::make(SomeRepository::class)` doesn't look that much better either.

With this little package, your `RouteBinder` objects can depend on any `Service` or `Repository` layer of your application.
Now, you could even test those bindings by mocking the dependencies and expecting a call to whatever `Repository::find()` method
you use on route resolution!

This may look like _waaaaaay_ too complicated a scenario right now, but trust me, you'll love it.
