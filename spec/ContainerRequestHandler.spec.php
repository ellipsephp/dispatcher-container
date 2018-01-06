<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher\ContainerRequestHandler;

describe('ContainerRequestHandler', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->handler = new ContainerRequestHandler($this->container->get(), 'SomeRequestHandler');

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->process()', function () {

        it('should get the request handler from the container and proxy its ->handle() method', function () {

            $request = mock(ServerRequestInterface::class)->get();
            $response = mock(ResponseInterface::class)->get();

            $handler = mock(RequestHandlerInterface::class);

            $this->container->get->with('SomeRequestHandler')->returns($handler);

            $handler->handle->with($request)->returns($response);

            $test = $this->handler->handle($request);

            expect($test)->toBe($response);

        });

    });

});
