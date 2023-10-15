<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Cancellation;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Closure;
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
    /** @var list<Closure(RequestInterface $request, callable $next): ResponseInterface> $middlewares */
    private array $middlewares = [];

    public function __construct(
        private readonly HttpProfiler $profiler,
        private readonly HttpClient $client,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Cancellation $cancellation,
    ) {
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware->process(...);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        // first class callable
        $handler = $this->handle(...);

        $queue = new MiddlewareQueue($this->middlewares, $handler);

        return $queue->handle($request);
    }

    /**
     * Handle request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    private function handle(RequestInterface $request): ResponseInterface
    {
        $ampRequest = $this->fromPsrRequest($request);

        // TODO: Set request parameters
        if (!$ampRequest->hasHeader('User-Agent')) {
            $ampRequest->setHeader('User-Agent', 'heavyrain/0.0.1');
        }
        $ampRequest->setBodySizeLimit(1024 * 1024);
        $ampRequest->setInactivityTimeout(10);
        $ampRequest->setTcpConnectTimeout(10);
        $ampRequest->setTlsHandshakeTimeout(10);
        $ampRequest->setTransferTimeout(10);

        try {
            $ampResponse = $this->client->request($ampRequest, $this->cancellation);

            // Profiles with HTTP events.
            $this->profiler->profile($ampRequest, $ampResponse);
        } catch (\Throwable $exception) {
            // Profile exception during request
            $this->profiler->profileException($ampRequest, $exception);

            throw new RequestException('failed to fetch response', previous: $exception);
        }

        return $this->toPsrResponse($ampResponse);
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
