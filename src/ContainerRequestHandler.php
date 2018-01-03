<?php declare(strict_types=1);

namespace Ellipse\Dispatcher;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\RequestHandlerInterface;

class ContainerRequestHandler implements RequestHandlerInterface
{
    /**
     * The container factory.
     *
     * @var \Ellipse\Dispatcher\ContainerFactory
     */
    private $factory;

    /**
     * The request handler class name.
     *
     * @var string
     */
    private $class;

    /**
     * Set up a container request handler with the given container factory and
     * class name.
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
     * Get a container from the factory then proxy the retrieved request
     * handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = ($this->factory)($request)->get($this->class);

        return $handler->handle($request);
    }
}
