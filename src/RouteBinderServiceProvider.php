<?php namespace GuiWoda\RouteBinder;

use Illuminate\Support\ServiceProvider;

final class RouteBinderServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/config/route-binder.php');
        $this->publishes([$source => config_path('route-binder.php')]);

        $this->bootRoutes();
    }

    /**
     * Register all routes
     */
    public function bootRoutes()
    {
        /** @type \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        /** @type \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        if ($config->has('route-binder')) {
            foreach ($config->get('route-binder') as $binder) {
                $this->app->make($binder)->bind($router);
            }
        }
    }
}
