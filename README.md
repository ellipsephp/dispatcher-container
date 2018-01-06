# Container resolver

This package provides a factory producing instances of [ellipse/dispatcher](https://github.com/ellipsephp/dispatcher) resolving [Psr-15 middleware](https://github.com/http-interop/http-server-middleware) and [Psr-15 request handler](https://github.com/http-interop/http-server-handler) class names as actual instances using a [Psr-11 container](http://www.php-fig.org/psr/psr-11/meta/).

**Require** php >= 7.1

**Installation** `composer require ellipse/dispatcher-container`

**Run tests** `./vendor/bin/kahlan`

- [Getting started](https://github.com/ellipsephp/dispatcher-container#getting-started)

## Getting started

This package provides an `Ellipse\Dispatcher\ContainerResolver` class implementing `Ellipse\DispatcherFactoryInterface` which allows to decorate any other instance implementing this interface.

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

// Assuming SomeMiddleware1 implements Interop\Http\Server\MiddlewareInterface:
$container->set(SomeMiddleware1::class, function ($container) {

    $dependency = $container->get(MiddlewareDependency::class);

    return new SomeMiddleware1($dependency);

});

// Assuming SomeRequestHandler implements Interop\Http\Server\RequestHandlerInterface:
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
