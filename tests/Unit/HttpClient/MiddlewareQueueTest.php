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
        $lastHandler = static fn (RequestInterface $request): ResponseInterface =>
            $this->createStub(ResponseInterface::class);

        $queue = new MiddlewareQueue([], $lastHandler);

        $actual = $queue->handle($this->createStub(RequestInterface::class));

        self::assertInstanceOf(ResponseInterface::class, $actual);
    }

    #[Test]
    public function testSomeMiddlewares(): void
    {
        self::markTestIncomplete('TODO');
    }
}
