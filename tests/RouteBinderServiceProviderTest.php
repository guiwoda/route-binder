<?php

use GuiWoda\RouteBinder\RouteBinder;
use GuiWoda\RouteBinder\RouteBinderServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Routing\Router;

class RouteBinderServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_should_make_all_routing_classes_and_call_bind_on_them()
    {
        $appMock         = \Mockery::mock(Container::class);
        $configMock      = \Mockery::mock(Repository::class);
        $routerMock      = \Mockery::mock(Router::class);
        $routeBinderMock = \Mockery::mock(RouteBinder::class);

        $routeBinders = [
            'fooBinder', 'barBinder', 'bazBinder',
        ];

        $appMock
            ->shouldReceive('offsetGet')
            ->with('config')
            ->once()
            ->andReturn($configMock);

        $appMock
            ->shouldReceive('offsetGet')
            ->with('router')
            ->once()
            ->andReturn($routerMock);

        $appMock
            ->shouldReceive('make')
            ->with(\Mockery::anyOf('fooBinder', 'barBinder', 'bazBinder'))
            ->andReturn($routeBinderMock);

        $configMock
            ->shouldReceive('get')
            ->with('route-binder::routes')
            ->andReturn($routeBinders);

        $routeBinderMock
            ->shouldReceive('bind')
            ->with($routerMock)
            ->times(count($routeBinders));

        $sp = new RouteBinderServiceProvider($appMock);

        $sp->bootRoutes();
    }
}
