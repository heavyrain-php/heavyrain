<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor\Middlewares;

use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(WaitSendRequestMiddleware::class)]
final class WaitSendRequestMiddlewareTest extends TestCase
{
    #[Test]
    public function testHandle(): void
    {
        $waitSec = 0.0001;
        $middleware = new WaitSendRequestMiddleware($waitSec);

        $result = $middleware->handleRequest(
            $this->createMock(RequestInterface::class),
            static function (RequestInterface $request): mixed { return 'A'; },
        );

        self::assertSame('A', $result);

        $result2 = $middleware->handleResponse(
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class),
            static function (RequestInterface $request, ResponseInterface $response): mixed { return 'B'; },
        );

        self::assertSame('B', $result2);
    }
}
