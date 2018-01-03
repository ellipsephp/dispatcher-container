<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

class ContainerMiddleware implements MiddlewareInterface
{
    /**
     * The container factory.
     *
     * @var \Ellipse\Dispatcher\ContainerFactory
     */
    private $factory;

    /**
     * The middleware class name.
     *
     * @var string
     */
    private $class;

    /**
     * Set up a container middleware with the given container factory and class
     * name.
     *
     * @param \Ellipse\Dispatcher\ContainerFactory  $factory
     * @param string                                $class
     */
    public function __construct(ContainerFactory $factory, string $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    /**
     * Get a container from the factory then proxy the retrieved middleware.
     *
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Interop\Http\Server\RequestHandlerInterface  $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = ($this->factory)($request)->get($this->class);

        return $middleware->process($request, $handler);
    }
}
