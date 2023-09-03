<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Heavyrain\HttpClient;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Heavyrain\Contracts\HttpClientInterface;
use Heavyrain\Contracts\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR-7 Adapter for Amp HTTP Client
 * @link https://github.com/amphp/http-client-psr7/blob/master/src/PsrAdapter.php
 */
final class AmphpClient implements HttpClientInterface
{
    /** @var MiddlewareInterface[] $middlewares */
    private array $middlewares = [];

    public function __construct(
        private readonly HttpClient $client,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $handler = fn (RequestInterface $request): ResponseInterface =>
            $this->toPsrResponse($this->client->request($this->fromPsrRequest($request)));

        $queue = new MiddlewareQueue($this->middlewares, $handler);

        return $queue->handle($request);
    }

    /**
     * From Psr Request to Amp Request
     *
     * @param RequestInterface $request
     * @return Request
     */
    private function fromPsrRequest(RequestInterface $request): Request
    {
        /** @var non-empty-string $method */
        $method = $request->getMethod();
        $body = $request->getBody()->__toString();
        $target = new Request(
            $request->getUri()->__toString(),
            $method,
            $body,
        );
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $target->setHeaders($request->getHeaders());

        return $target;
    }

    /**
     * From Amp Response to Psr Response
     *
     * @param Response $response
     * @return ResponseInterface
     */
    private function toPsrResponse(Response $response): ResponseInterface
    {
        $psrResponse = $this->responseFactory->createResponse(
            $response->getStatus(),
            $response->getReason(),
        )->withProtocolVersion($response->getProtocolVersion());

        foreach ($response->getHeaders() as $name => $lines) {
            $psrResponse = $psrResponse->withAddedHeader($name, $lines);
        }

        while (null !== $data = $response->getBody()->read()) {
            $psrResponse->getBody()->write($data);
        }

        $psrResponse->getBody()->rewind();

        return $psrResponse;
    }
}
