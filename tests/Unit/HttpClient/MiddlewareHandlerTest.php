<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(MiddlewareHandler::class)]
final class MiddlewareHandlerTest extends TestCase
{
    #[Test]
    public function testEmptyMiddlewares(): void
    {
        $handler = fn (RequestInterface $request): ResponseInterface =>
            $this->createStub(ResponseInterface::class);

        $middlewareHandler = new MiddlewareHandler($handler);

        $actual = $middlewareHandler->handle($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual);

        $actual2 = $middlewareHandler($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual2);
    }

    #[Test]
    public function testSomeMiddlewares(): void
    {
        $handler = fn (RequestInterface $request): ResponseInterface =>
            $this->createStub(ResponseInterface::class);
        $middleware = function (RequestInterface $request, callable $next): ResponseInterface {
            self::assertTrue(true);
            return $next($request);
        };

        $middlewareHandler = new MiddlewareHandler($handler, $middleware);

        $actual = $middlewareHandler->handle($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }
}
