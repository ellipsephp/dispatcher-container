<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

class ContainerMiddleware implements MiddlewareInterface
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The middleware class name.
     *
     * @var string
     */
    private $class;

    /**
     * Set up a container middleware with the given container and class name.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $class
     */
    public function __construct(ContainerInterface $container, string $class)
    {
        $this->container = $container;
        $this->class = $class;
    }

    /**
     * Get a middleware from the container then proxy its ->process() method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Interop\Http\Server\RequestHandlerInterface  $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->container->get($this->class)->process($request, $handler);
    }
}
