<?php

use function Eloquent\Phony\Kahlan\mock;
use function Eloquent\Phony\Kahlan\onStatic;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher;
use Ellipse\DispatcherFactoryInterface;
use Ellipse\Dispatcher\ContainerResolver;
use Ellipse\Dispatcher\ContainerRequestHandler;
use Ellipse\Middleware\ContainerMiddleware;

describe('ContainerResolver', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class)->get();

        $this->delegate = mock(DispatcherFactoryInterface::class);

        $this->resolver = new ContainerResolver($this->container, $this->delegate->get());

    });

    it('should implement DispatcherFactoryInterface', function () {

        expect($this->resolver)->toBeAnInstanceOf(DispatcherFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        beforeEach(function () {

            $this->dispatcher = mock(Dispatcher::class)->get();

        });

        context('when the given request handler is not a request handler class name', function () {

            it('should proxy the delegate with the given request handler', function () {

                $this->delegate->__invoke->with('handler', '~')->returns($this->dispatcher);

                $test = ($this->resolver)('handler', []);

                expect($test)->toBe($this->dispatcher);

            });

        });

        context('when the given request handler is a request handler class name', function () {

            it('should proxy the delegate with the given request handler wrapped into a ContainerRequestHandler', function () {

                $class = onStatic(mock(RequestHandlerInterface::class))->className();

                $handler = new ContainerRequestHandler($this->container, $class);

                $this->delegate->__invoke->with($handler, '~')->returns($this->dispatcher);

                $test = ($this->resolver)($class, []);

                expect($test)->toBe($this->dispatcher);

            });

        });

        context('when no middleware queue is given', function () {

            it('should proxy the delegate with an empty array', function () {

                $this->delegate->__invoke->with('~', [])->returns($this->dispatcher);

                $test = ($this->resolver)('handler');

                expect($test)->toBe($this->dispatcher);

            });

        });

        context('when an middleware queue is given', function () {

            it('should proxy the delegate with ContainerMiddleware wrapped around the middleware class names of the middleware queue', function () {

                $class = onStatic(mock(MiddlewareInterface::class))->className();

                $this->delegate->__invoke
                    ->with('~', ['middleware', new ContainerMiddleware($this->container, $class)])
                    ->returns($this->dispatcher);

                $test = ($this->resolver)('handler', ['middleware', $class]);

                expect($test)->toBe($this->dispatcher);

            });

        });

    });

});
