# Container resolver

This package provides a factory decorator for objects implementing `Ellipse\DispatcherFactoryInterface` from [ellipse/dispatcher](https://github.com/ellipsephp/dispatcher) package.

The resulting factory uses a [Psr-11](http://www.php-fig.org/psr/psr-11/) container to produce instances of `Ellipse\Dispatcher` using class names as [Psr-15](https://www.php-fig.org/psr/psr-15/) middleware and request handler.

**Require** php >= 7.0

**Installation** `composer require ellipse/dispatcher-container`

**Run tests** `./vendor/bin/kahlan`

- [Create a dispatcher using Psr-15 class names](https://github.com/ellipsephp/dispatcher-container#create-a-dispatcher-using-Psr-15-class-names)

## Create a dispatcher using Psr-15 class names

This package provides an `Ellipse\Dispatcher\ContainerResolver` class implementing `Ellipse\DispatcherFactoryInterface` which allows to decorate any other object implementing this interface.

It takes a container implementing `Psr\Container\ContainerInterface` as first parameter and the factory to decorate as second parameter.

Once decorated, the resulting dispatcher factory can be used to produce instances of `Ellipse\Dispatcher` using Psr-15 middleware and request handler class names.


```php
<?php

namespace App;

use SomePsr11Container;

use Ellipse\DispatcherFactory;
use Ellipse\Dispatcher\ContainerResolver;

// Get some incoming Psr-7 request.
$request = some_psr7_request_factory();

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Assuming SomeMiddleware1 implements Psr\Http\Server\MiddlewareInterface:
$container->set(SomeMiddleware1::class, function ($container) {

    $dependency = $container->get(MiddlewareDependency::class);

    return new SomeMiddleware1($dependency);

});

// Assuming SomeRequestHandler implements Psr\Http\Server\RequestHandlerInterface:
$container->set(SomeRequestHandler::class, function () {

    $dependency = $container->get(RequestHandlerDependency::class);

    return new SomeRequestHandler($dependency);

});

// Get a decorated dispatcher factory.
$factory = new ContainerResolver($container, new DispatcherFactory);

// A dispatcher using both class names and Psr-15 instances can now be created.
$dispatcher = $factory(SomeRequestHandler::class, [
    SomeMiddleware1::class,
    new SomeMiddleware2,
]);

// This works :-)
$dispatcher->handle($request);
```
