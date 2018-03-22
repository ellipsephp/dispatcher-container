# Container resolver

This package provides a factory decorator for objects implementing `Ellipse\DispatcherFactoryInterface` from [ellipse/dispatcher](https://github.com/ellipsephp/dispatcher) package.

The resulting factory uses a [Psr-11](http://www.php-fig.org/psr/psr-11/) container to produce instances of `Ellipse\Dispatcher` using class names as [Psr-15](https://www.php-fig.org/psr/psr-15/) middleware and request handler.

**Require** php >= 7.0

**Installation** `composer require ellipse/dispatcher-container`

**Run tests** `./vendor/bin/kahlan`

- [Create a dispatcher using Psr-15 class names](https://github.com/ellipsephp/dispatcher-container#create-a-dispatcher-using-Psr-15-class-names)
- [Example using auto wiring](#example-using-auto-wiring)

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

// Middleware and request handler are retrieved from the container.
$dispatcher->handle($request);
```

## Example using auto wiring

It can be cumbersome to register every middleware and request handler classes in the container. Here is how to auto wire middleware and request handler instances using the `Ellipse\Container\ReflectionContainer` class from the [ellipse/container-reflection](https://github.com/ellipsephp/container-reflection) package.

```php
<?php

namespace App;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use SomePsr11Container;

use Ellipse\DispatcherFactory;
use Ellipse\Dispatcher\ContainerResolver;
use Ellipse\Container\ReflectionContainer;

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Decorate the container with a reflection container.
// Specify the middleware and request handler implementations can be auto wired.
$reflection = new ReflectionContainer($container, [
    MiddlewareInterface::class,
    RequestHandlerInterface::class,
]);

// Create a container resolver using the reflection container.
$factory = new ContainerResolver($reflection, new DispatcherFactory);

// Instances of SomeMiddleware and SomeRequestHandler are built using auto wiring.
$dispatcher = $factory(SomeRequestHandler::class, [SomeMiddleware::class]);
```
