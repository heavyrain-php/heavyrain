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

#[CoversClass(MiddlewareQueue::class)]
final class MiddlewareQueueTest extends TestCase
{
    #[Test]
    public function testEmptyMiddlewares(): void
    {
        $lastHandler = fn (RequestInterface $request): ResponseInterface =>
            $this->createStub(ResponseInterface::class);

        $queue = new MiddlewareQueue([], $lastHandler);

        $actual = $queue->handle($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }

    #[Test]
    public function testSomeMiddlewares(): void
    {
        $count = 0;
        $middleware1 = function (RequestInterface $request, callable $next) use (&$count): ResponseInterface {
            self::assertSame(0, $count++);
            $response = $next($request);
            self::assertSame(3, $count++);
            return $response;
        };

        $middleware2 = function (RequestInterface $request, callable $next) use (&$count): ResponseInterface {
            self::assertSame(1, $count++);
            $response = $next($request);
            self::assertSame(2, $count++);
            return $response;
        };

        $lastHandler = fn (RequestInterface $request): ResponseInterface =>
            $this->createStub(ResponseInterface::class);

        $queue = new MiddlewareQueue([$middleware1, $middleware2], $lastHandler);

        $actual = $queue->handle($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }
}
