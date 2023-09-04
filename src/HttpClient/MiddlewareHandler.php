<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Heavyrain\HttpClient;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware handler
 */
final class MiddlewareHandler
{
    /**
     * @param MiddlewareHandler|Closure(RequestInterface $request): ResponseInterface $handler
     * @param null|Closure(RequestInterface $request, callable $next): ResponseInterface $middleware
     */
    public function __construct(
        private readonly MiddlewareHandler|Closure $handler,
        private readonly ?Closure $middleware = null,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        // first class callable
        $handler = ($this->handler)(...);

        if (\is_null($this->middleware)) {
            return $handler($request);
        }

        return ($this->middleware)($request, $handler);
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
