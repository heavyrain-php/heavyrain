<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Tests\TestCase;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(ResponseAssertionException::class)]
#[CoversClass(AssertableResponse::class)]
final class AssertableResponseTest extends TestCase
{
    #[Test]
    public function testGetRaw(): void
    {
        $response = new AssertableResponse(
            $psrRequest = $this->createMock(RequestInterface::class),
            $psrResponse = $this->createMock(ResponseInterface::class),
        );

        self::assertSame($psrRequest, $response->getRawRequest());
        self::assertSame($psrResponse, $response->getRawResponse());
    }

    #[Test]
    public function testGetJsonBody(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface */
        $psrResponse = $this->createMock(ResponseInterface::class);
        $response = new AssertableResponse(
            $this->createMock(RequestInterface::class),
            $psrResponse,
        );

        $psrResponse->expects($this->once())
            ->method('hasHeader')
            ->with('Content-Type')
            ->willReturn(true);
        $psrResponse->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/json');
        $psrResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('{"hello":"world"}');

        $actual = $response->getJsonBody();

        self::assertSame(['hello' => 'world'], $actual);
    }

    #[Test]
    public function testGetJsonBodyNullError(): void
    {
        $this->expectException(ResponseAssertionException::class);
        $this->expectExceptionMessage('Response body is empty');

        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface */
        $psrResponse = $this->createMock(ResponseInterface::class);
        $response = new AssertableResponse(
            $this->createMock(RequestInterface::class),
            $psrResponse,
        );

        $psrResponse->expects($this->once())
            ->method('hasHeader')
            ->with('Content-Type')
            ->willReturn(true);
        $psrResponse->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/json');
        $psrResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('');

        $response->getJsonBody();
    }

    #[Test]
    public function testGetJsonBodyDecodeError(): void
    {
        $this->expectException(ResponseAssertionException::class);
        $this->expectExceptionMessage('Failed to decode JSON body code:4 message:Syntax error');

        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface */
        $psrResponse = $this->createMock(ResponseInterface::class);
        $response = new AssertableResponse(
            $this->createMock(RequestInterface::class),
            $psrResponse,
        );

        $psrResponse->expects($this->once())
            ->method('hasHeader')
            ->with('Content-Type')
            ->willReturn(true);
        $psrResponse->expects($this->once())
            ->method('getHeaderLine')
            ->with('Content-Type')
            ->willReturn('application/json');
        $psrResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('{{{');

        $response->getJsonBody();
    }

    #[Test]
    public function testAssertJsonHasKey(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('{"hello":"world"}');

        $psrResponse = new Response($stream, 200, ['Content-Type' => ['application/json; charset=UTF-8']]);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertJsonHasKey('hello');

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testAssertBodyContains(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface */
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn('{"hello":"world"}');

        $psrResponse = new Response($stream, 200, ['Content-Type' => ['application/json; charset=UTF-8']]);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertBodyContains('world');

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testAssertIsHtml(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $psrResponse = new Response($stream, 200, ['Content-Type' => ['text/html; charset=UTF-8']]);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertIsHtml();

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testAssertJson(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $psrResponse = new Response($stream, 200, ['Content-Type' => ['application/json']]);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertIsJson();

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testAssertOk(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $psrResponse = new Response($stream);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertOk();

        self::assertSame($response, $actual);
    }

    #[Test]
    public function testAssertStatusCode(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $psrResponse = new Response($stream, 201);

        $response = new AssertableResponse($this->createMock(RequestInterface::class), $psrResponse);

        $actual = $response->assertStatusCode(201);

        self::assertSame($response, $actual);
    }
}
