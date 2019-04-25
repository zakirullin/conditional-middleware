<?php
declare(strict_types=1);

namespace Tests;

use Closure;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ConditionalTest
 *
 * @package Tests
 */
class ConditionalTest extends TestCase
{
    /**
     * @covers \Zakirullin\Middlewares\Conditional::process()
     * @covers \Zakirullin\Middlewares\Conditional::__construct
     * @covers \Zakirullin\Middlewares\Conditional::createMiddlewareFromClosure
     */
    public function testUse()
    {
        $assertMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            return $handler->handle($request);
        };
        $conditionalMiddleware = new \Zakirullin\Middlewares\Conditional(
            function () {
                return true;
            },
            function () use ($assertMiddleware) {
                return $assertMiddleware;
            }
        );

        $request = Factory::createServerRequest('GET', '/');
        $appMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $response = Factory::createResponse();
            $response->getBody()->write('success');

            return $response;
        };

        $response = Dispatcher::run(
            [
                $conditionalMiddleware,
                $appMiddleware
            ],
            $request
        );

        self::assertEquals((string)$response->getBody(), 'success');
    }

    /**
     * @covers \Zakirullin\Middlewares\Conditional::process()
     * @covers \Zakirullin\Middlewares\Conditional::__construct
     * @covers \Zakirullin\Middlewares\Conditional::createMiddlewareFromClosure
     */
    public function testDontUse()
    {
        $assertMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            self::assertTrue(false);

            return $handler->handle($request);
        };
        $conditionalMiddleware = new \Zakirullin\Middlewares\Conditional(
            function () {
                return false;
            },
            function () use ($assertMiddleware) {
                return $assertMiddleware;
            }
        );

        $request = Factory::createServerRequest('GET', '/');
        $appMiddleware = function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $response = Factory::createResponse();
            $response->getBody()->write('success');

            return $response;
        };

        $response = Dispatcher::run(
            [
                $conditionalMiddleware,
                $appMiddleware
            ],
            $request
        );

        self::assertEquals((string)$response->getBody(), 'success');
    }
}
