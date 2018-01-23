<?php

use function Eloquent\Phony\Kahlan\mock;
use function Eloquent\Phony\Kahlan\onStatic;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Dispatcher;
use Ellipse\DispatcherFactoryInterface;
use Ellipse\Dispatcher\ContainerResolver;
use Ellipse\Dispatcher\ContainerRequestHandler;
use Ellipse\Dispatcher\ContainerMiddlewareGenerator;

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

        context('when no iterable list of middleware is given', function () {

            it('should proxy the delegate with an empty array wrapped into a container middleware generator', function () {

                $generator = new ContainerMiddlewareGenerator($this->container, []);

                $this->delegate->__invoke->with('~', $generator)->returns($this->dispatcher);

                $test = ($this->resolver)('handler');

                expect($test)->toBe($this->dispatcher);

            });

        });

        context('when an iterable list of middleware is given', function () {

            it('should proxy the delegate with the given iterable list of middleware wrapped into a container middleware generator', function () {

                $test = function ($middleware) {

                    $generator = new ContainerMiddlewareGenerator($this->container, $middleware);

                    $this->delegate->__invoke->with('~', $generator)->returns($this->dispatcher);

                    $test = ($this->resolver)('handler', $middleware);

                    expect($test)->toBe($this->dispatcher);

                };

                $middleware = ['middleware1', 'middleware2'];

                $test($middleware);
                $test(new ArrayIterator($middleware));
                $test(new class ($middleware) implements IteratorAggregate
                {
                    public function __construct($middleware) { $this->middleware = $middleware; }
                    public function getIterator() { return new ArrayIterator($this->middleware); }
                });

            });

        });

    });

});
