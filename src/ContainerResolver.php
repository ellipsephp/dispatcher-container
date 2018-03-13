<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher;
use Ellipse\DispatcherFactoryInterface;
use Ellipse\Middleware\ContainerMiddleware;
use Ellipse\Handlers\ContainerRequestHandler;

class ContainerResolver implements DispatcherFactoryInterface
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The delegate.
     *
     * @var \Ellipse\DispatcherFactoryInterface
     */
    private $delegate;

    /**
     * Set up a container resolver with the given container and delegate.
     *
     * @param \Psr\Container\ContainerInterface     $container
     * @param \Ellipse\DispatcherFactoryInterface   $delegate
     */
    public function __construct(ContainerInterface $container, DispatcherFactoryInterface $delegate)
    {
        $this->container = $container;
        $this->delegate = $delegate;
    }

    /**
     * Proxy the delegate by wrapping container request handler and container
     * middleware around the given request handler and middleware queue.
     *
     * @param mixed $handler
     * @param array $middleware
     * @return \Ellipse\Dispatcher
     */
    public function __invoke($handler, array $middleware = []): Dispatcher
    {
        $handler = is_string($handler) && is_subclass_of($handler, RequestHandlerInterface::class, true)
            ? new ContainerRequestHandler($this->container, $handler)
            : $handler;

        $middleware = array_map(function ($middleware) {

            return is_string($middleware) && is_subclass_of($middleware, MiddlewareInterface::class, true)
                ? new ContainerMiddleware($this->container, $middleware)
                : $middleware;

        }, $middleware);

        return ($this->delegate)($handler, $middleware);
    }
}
