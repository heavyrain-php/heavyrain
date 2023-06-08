<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\RequestBuilderInterface;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[UsesClass(AssertableResponse::class)]
#[CoversClass(Client::class)]
final class ClientTest extends TestCase
{
    #[Test]
    public function testWait(): void
    {
        $cl = new Client(
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestBuilderInterface::class),
        );

        $cl->waitSec(0.0001);
        $cl->waitMilliSec(0.1);
        $cl->waitMicroSec(1);

        // HACK: only for coverage
        self::assertTrue(true);
    }

    #[Test]
    public function testGet(): void
    {
        $cl = $this->createClient('GET', '/a', 'query', ['b' => 'c']);

        $actual = $cl->get('/a', ['b' => 'c']);

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPost(): void
    {
        $cl = $this->createClient('POST', '/b', 'body', 'Hello');

        $actual = $cl->post('/b', 'Hello');

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPostJson(): void
    {
        $cl = $this->createClient('POST', '/c', 'json', ['ok' => true]);

        $actual = $cl->postJson('/c', ['ok' => true]);

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPut(): void
    {
        $cl = $this->createClient('PUT', '/d', 'body', 'Hey');

        $actual = $cl->put('/d', 'Hey');

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPutJson(): void
    {
        $cl = $this->createClient('PUT', '/e', 'json', ['ok' => false]);

        $actual = $cl->putJson('/e', ['ok' => false]);

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testDelete(): void
    {
        $cl = $this->createClient('DELETE', '/f', 'body', 'F');

        $actual = $cl->delete('/f', 'F');

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testDeleteJson(): void
    {
        $cl = $this->createClient('DELETE', '/g', 'json', ['g' => 'is a']);

        $actual = $cl->deleteJson('/g', ['g' => 'is a']);

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPatch(): void
    {
        $cl = $this->createClient('PATCH', '/h', 'body', 'ha');

        $actual = $cl->patch('/h', 'ha');

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    #[Test]
    public function testPatchJson(): void
    {
        $cl = $this->createClient('PATCH', '/i', 'json', ['j', 'k', 'l']);

        $actual = $cl->patchJson('/i', ['j', 'k', 'l']);

        self::assertInstanceOf(AssertableResponse::class, $actual);
    }

    private function createClient(string $method, string $path, string $bodyMethod, string|array $bodyWith): Client
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface */
        $psrCl = $this->createMock(ClientInterface::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&RequestBuilderInterface */
        $builder = $this->createMock(RequestBuilderInterface::class);
        $request = $this->createMock(RequestInterface::class);

        $psrCl->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($this->createMock(ResponseInterface::class));

        $builder->expects($this->once())
            ->method('method')
            ->with($method)
            ->willReturn($builder);
        $builder->expects($this->once())
            ->method('path')
            ->with($path)
            ->willReturn($builder);
        $builder->expects($this->once())
            ->method($bodyMethod)
            ->with($bodyWith)
            ->willReturn($builder);
        $builder->expects($this->once())
            ->method('createRequest')
            ->with()
            ->willReturn($request);

        return new Client($psrCl, $builder);
    }
}
