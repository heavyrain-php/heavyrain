<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor\Middlewares;

use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class IdentifyRequestMiddleware implements MiddlewareInterface
{
    /**
     * Unique Client ID
     *
     * @param string $clientId
     */
    public function __construct(private readonly string $clientId)
    {
    }

    public function handleRequest(RequestInterface $request, callable $next): mixed
    {
        return $next($request->withHeader('Heavyrain-Request-Id', \uniqid($this->clientId, false)));
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): mixed
    {
        return $next($request, $response);
    }
}
