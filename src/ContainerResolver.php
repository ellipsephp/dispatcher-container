<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher;
use Ellipse\DispatcherFactoryInterface;

class ContainerResolver implements DispatcherFactoryInterface
{
    /**
     * The container factory.
     *
     * @var \Ellipse\Dispatcher\ContainerFactory
     */
    private $factory;

    /**
     * The delegate.
     *
     * @var \Ellipse\DispatcherFactoryInterface
     */
    private $delegate;

    /**
     * Set up a dispatcher factory resolving class names with the given factory
     * and delegate.
     *
     * @param callable                              $factory
     * @param \Ellipse\DispatcherFactoryInterface   $delegate
     */
    public function __construct(callable $factory, DispatcherFactoryInterface $delegate)
    {
        $this->factory = new ContainerFactory($factory);
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
        $middleware = new ContainerMiddlewareGenerator($this->factory, $middleware);

        if (is_string($handler) && is_a($handler, RequestHandlerInterface::class, true)) {

            $handler = new ContainerRequestHandler($this->factory, $handler);

        }

        return ($this->delegate)($handler, $middleware);
    }
}
