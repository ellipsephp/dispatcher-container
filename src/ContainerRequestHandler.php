<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContainerRequestHandler implements RequestHandlerInterface
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The request handler class name.
     *
     * @var string
     */
    private $class;

    /**
     * Set up a container request handler with the given container and class
     * name.
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
     * Get a request handler from the container then proxy its ->handle()
     * method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->container->get($this->class)->handle($request);
    }
}
