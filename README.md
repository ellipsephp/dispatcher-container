# Container resolver

This package provides a factory decorator for objects implementing `Ellipse\DispatcherFactoryInterface` from [ellipse/dispatcher](https://github.com/ellipsephp/dispatcher) package. It allows to produce instances of `Ellipse\Dispatcher` using middleware and request handler class names.

**Require** php >= 7.0

**Installation** `composer require ellipse/dispatcher-container`

**Run tests** `./vendor/bin/kahlan`

- [Create a dispatcher factory resolving Psr-15 class names](#create-a-dispatcher-factory-resolving-psr-15-class-names)

## Create a dispatcher factory resolving Psr-15 class names

This package provides an `Ellipse\Dispatcher\ContainerResolver` class implementing `Ellipse\DispatcherFactoryInterface` which allows to decorate any other object implementing this interface.

It takes a container implementing `Psr\Container\ContainerInterface` as first parameter and the factory to decorate as second parameter.

Once decorated, the resulting dispatcher factory can be used to produce instances of `Ellipse\Dispatcher` by resolving middleware class names as `Ellipse\Middleware\ContainerMiddleware` from the [ellipse/middleware-container](https://github.com/ellipsephp/middleware-container) package and request handler class names as `Ellipse\Handlers\ContainerRequestHandler` from the [ellipse/handlers-container](https://github.com/ellipsephp/handlers-container) package.

`ContainerMiddleware` and `ContainerRequestHandler` logic is described on the [ellipse/middleware-container](https://github.com/ellipsephp/middleware-container#using-container-entries-as-middleware) and [ellipse/handlers-container](https://github.com/ellipsephp/handlers-container#using-container-entries-as-request-handlers) documentation pages.

```php
<?php

namespace App;

use SomePsr11Container;

use Ellipse\DispatcherFactory;
use Ellipse\Dispatcher\ContainerResolver;

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Decorate a DispatcherFactoryInterface implementation with a ContainerResolver.
$factory = new ContainerResolver($container, new DispatcherFactory);

// A dispatcher using both class names and Psr-15 instances can now be created.
$dispatcher = $factory(SomeRequestHandler::class, [SomeMiddleware1::class, new SomeMiddleware2]);
```
