<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Tests\TestCase;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\UriFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(RequestBuilder::class)]
final class RequestBuilderTest extends TestCase
{
    #[Test]
    public function testCreateUri(): void
    {
        $builder = new RequestBuilder(
            new UriFactory(),
            $this->createMock(StreamFactoryInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            'https://localhost:8080',
        );

        $actual = $builder
            ->path('test')
            ->query(['a' => 'b', 'c' => ''])
            ->userInfo('admin', 'secret')
            ->fragment('header')
            ->createUri();

        $expected = 'https://admin:secret@localhost:8080/test?a=b&c=#header';
        self::assertSame($expected, $actual->__toString());
    }

    #[Test]
    public function testCreateUriExplicitly(): void
    {
        $builder = new RequestBuilder(
            $this->createMock(UriFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            'https://localhost',
        );

        $expected = $this->createMock(UriInterface::class);
        $actual = $builder->uri($expected)->createUri();

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function testCreateRequest(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('{"b":"c"}');

        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamFactoryInterface */
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->willReturn($stream);

        $builder = new RequestBuilder(
            $this->createMock(UriFactoryInterface::class),
            $streamFactory,
            new RequestFactory(),
            'https://localhost',
        );

        $request = $builder
            ->method('POST')
            ->requestTarget('')
            ->protocolVersion('1.0')
            ->headers(['Content-Length' => '1'])
            ->json(['b' => 'c'])
            ->createRequest();

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('{"b":"c"}', (string)$request->getBody());
        self::assertSame(['Content-Length' => ['1'], 'Content-Type' => ['application/json; charset=UTF-8'], 'Accept' => ['application/json; charset=UTF-8']], $request->getHeaders());
        self::assertSame('POST', $request->getMethod());
        self::assertSame('1.0', $request->getProtocolVersion());
        self::assertSame('', $request->getRequestTarget());
    }

    #[Test]
    public function testHeader(): void
    {
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->basic('admin', 'secret'),
            'Authorization',
            'Basic YWRtaW46c2VjcmV0',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->bearer('token'),
            'Authorization',
            'Bearer token',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->authorization('Bearer tttt'),
            'Authorization',
            'Bearer tttt',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->contentTypePlain(),
            'Content-Type',
            'text/plain',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->contentTypeHtml(),
            'Content-Type',
            'text/html',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->contentTypeJson('UTF-8'),
            'Content-Type',
            'application/json; charset=UTF-8',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->contentTypeJson(null),
            'Content-Type',
            'application/json',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->contentType('application/json'),
            'Content-Type',
            'application/json',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->acceptAll(),
            'Accept',
            '*/*',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->acceptText(),
            'Accept',
            'text/*',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->acceptHtml(),
            'Accept',
            'text/html',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->acceptJson(),
            'Accept',
            'application/json; charset=UTF-8',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->acceptJson(null),
            'Accept',
            'application/json',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->accept('text/html'),
            'Accept',
            'text/html',
        );
        $this->assertHeaderHas(
            static fn (RequestBuilder $builder): RequestBuilder => $builder->userAgent('heavyrain'),
            'User-Agent',
            'heavyrain',
        );
    }

    private function assertHeaderHas(callable $callback, string $name, string $value): void
    {
        $builder = new RequestBuilder(
            $this->createMock(UriFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            new RequestFactory(),
            'https://localhost',
        );

        $callback($builder);

        $request = $builder->createRequest();
        $header = $request->getHeaderLine($name);

        self::assertSame($value, $header);
    }
}
