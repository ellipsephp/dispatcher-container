<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use IteratorAggregate;

use Interop\Http\Server\MiddlewareInterface;

class ContainerMiddlewareGenerator implements IteratorAggregate
{
    /**
     * The container factory.
     *
     * @var \Ellipse\Dispatcher\ContainerFactory
     */
    private $factory;

    /**
     * The iterable list of middleware which may be a middleware class name.
     *
     * @var iterable
     */
    private $middleware;

    /**
     * Set up a container middleware with the given container factory and
     * iterable list of middleware.
     *
     * @param \Ellipse\Dispatcher\ContainerFactory  $factory
     * @param iterable                              $middleware
     */
    public function __construct(ContainerFactory $factory, iterable $middleware)
    {
        $this->factory = $factory;
        $this->middleware = $middleware;
    }

    /**
     * This is a generator proxying the iterable list of middleware by wrapping
     * the middleware class names inside a container middleware.
     */
    public function getIterator()
    {
        foreach ($this->middleware as $middleware) {

            yield is_string($middleware) && is_a($middleware, MiddlewareInterface::class, true)
                ? new ContainerMiddleware($this->factory, $middleware)
                : $middleware;

        }
    }
}
