<?php
declare(strict_types = 1);

namespace Zakirullin\Middlewares;

use Closure;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Conditional implements MiddlewareInterface
{
    /**
     * @var callable
     */
    protected $shouldUseCallback;

    /**
     * @var callable
     */
    protected $getMiddlewareCallback;

    /**
     * @param callable $shouldUseCallback
     * @param callable $getMiddlewareCallback
     */
    public function __construct(
        callable $shouldUseCallback,
        callable $getMiddlewareCallback
    ) {
        $this->shouldUseCallback = $shouldUseCallback;
        $this->getMiddlewareCallback = $getMiddlewareCallback;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $shouldUse = call_user_func($this->shouldUseCallback, $request);
        if ($shouldUse) {
            /**
             * @var MiddlewareInterface $middleware
             */
            $middleware = call_user_func($this->getMiddlewareCallback);
            if ($middleware instanceof Closure) {
                $middleware = $this->createMiddlewareFromClosure($middleware);
            }

            return $middleware->process($request, $handler);
        } else {
            return $handler->handle($request);
        }
    }

    /**
     * @param Closure $handler
     * @return MiddlewareInterface
     */
    protected function createMiddlewareFromClosure(Closure $handler): MiddlewareInterface
    {
        return new class($handler) implements MiddlewareInterface {
            private $handler;

            public function __construct(Closure $handler)
            {
                $this->handler = $handler;
            }

            public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
            {
                return call_user_func($this->handler, $request, $next);
            }
        };
    }
}