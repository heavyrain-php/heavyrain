<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Http\Client\ApplicationInterceptor;
use Amp\Http\Client\Connection\Connection;
use Amp\Http\Client\Connection\Stream;
use Amp\Http\Client\EventListener;
use Amp\Http\Client\NetworkInterceptor;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;

/**
 * Profiles with HTTP events.
 */
final class AmphpEventListener implements EventListener
{
    public function requestStart(Request $request): void
    {
        if (!$request->hasAttribute(HttpResult::START_KEY)) {
            // records the start time of the request
            $request->setAttribute(HttpResult::START_KEY, \microtime(true));
        }
    }

    public function requestFailed(Request $request, \Throwable $exception): void
    {
        // Do nothing.
    }

    public function requestEnd(Request $request, Response $response): void
    {
        // Do nothing.
    }

    public function requestRejected(Request $request): void
    {
        // Do nothing.
    }

    public function applicationInterceptorStart(Request $request, ApplicationInterceptor $interceptor): void
    {
        // Do nothing.
    }

    public function applicationInterceptorEnd(Request $request, ApplicationInterceptor $interceptor, Response $response): void
    {
        // Do nothing.
    }

    public function networkInterceptorStart(Request $request, NetworkInterceptor $interceptor): void
    {
        // Do nothing.
    }

    public function networkInterceptorEnd(Request $request, NetworkInterceptor $interceptor, Response $response): void
    {
        // Do nothing.
    }

    public function connectionAcquired(Request $request, Connection $connection, int $streamCount): void
    {
        $request->setAttribute(HttpResult::CONNECT_KEY, $connection->getConnectDuration());
    }

    public function push(Request $request): void
    {
        // Do nothing.
    }

    public function requestHeaderStart(Request $request, Stream $stream): void
    {
        $request->setAttribute(HttpResult::SEND_START_KEY, \microtime(true));
    }

    public function requestHeaderEnd(Request $request, Stream $stream): void
    {
        // Do nothing.
    }

    public function requestBodyStart(Request $request, Stream $stream): void
    {
        // Do nothing.
    }

    public function requestBodyProgress(Request $request, Stream $stream): void
    {
        // Do nothing.
    }

    public function requestBodyEnd(Request $request, Stream $stream): void
    {
        $request->setAttribute(HttpResult::SEND_END_KEY, \microtime(true));
    }

    public function responseHeaderStart(Request $request, Stream $stream): void
    {
        $request->setAttribute(HttpResult::RECEIVE_START_KEY, \microtime(true));
    }

    public function responseHeaderEnd(Request $request, Stream $stream, Response $response): void
    {
        // Do nothing.
    }

    public function responseBodyStart(Request $request, Stream $stream, Response $response): void
    {
        // Do nothing.
    }

    public function responseBodyProgress(Request $request, Stream $stream, Response $response): void
    {
        // Do nothing.
    }

    public function responseBodyEnd(Request $request, Stream $stream, Response $response): void
    {
        $request->setAttribute(HttpResult::RECEIVE_END_KEY, \microtime(true));
    }
}
