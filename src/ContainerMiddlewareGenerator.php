<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use IteratorAggregate;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\MiddlewareInterface;

use Ellipse\Middleware\ContainerMiddleware;

class ContainerMiddlewareGenerator implements IteratorAggregate
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The iterable list of middleware which may be a middleware class name.
     *
     * @var iterable
     */
    private $middleware;

    /**
     * Set up a container middleware with the given container and iterable list
     * of middleware.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param iterable                          $middleware
     */
    public function __construct(ContainerInterface $container, iterable $middleware)
    {
        $this->container = $container;
        $this->middleware = $middleware;
    }

    /**
     * This is a generator proxying the iterable list of middleware by wrapping
     * the middleware class names inside a container middleware.
     */
    public function getIterator()
    {
        foreach ($this->middleware as $middleware) {

            yield is_string($middleware) && is_subclass_of($middleware, MiddlewareInterface::class, true)
                ? new ContainerMiddleware($this->container, $middleware)
                : $middleware;

        }
    }
}
