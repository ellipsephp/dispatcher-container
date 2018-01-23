<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher;
use Ellipse\DispatcherFactoryInterface;

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
     * Proxy the delegate by wrapping request handler class names and iterable
     * list of middleware into container resolvers.
     *
     * @param mixed     $handler
     * @param iterable  $middleware
     * @return \Ellipse\Dispatcher
     */
    public function __invoke($handler, iterable $middleware = []): Dispatcher
    {
        $middleware = new ContainerMiddlewareGenerator($this->container, $middleware);

        if (is_string($handler) && is_subclass_of($handler, RequestHandlerInterface::class, true)) {

            $handler = new ContainerRequestHandler($this->container, $handler);

        }

        return ($this->delegate)($handler, $middleware);
    }
}
