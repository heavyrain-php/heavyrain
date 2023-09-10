<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware queue
 */
final class MiddlewareQueue
{
    /**
     * @param Closure[] $middlewares
     * @psalm-param list<Closure(RequestInterface $request, callable $next): ResponseInterface> $middlewares
     * @phpstan-param list<Closure(RequestInterface $request, callable $next): ResponseInterface> $middlewares
     * @phan-param list<Closure(RequestInterface $request, callable $next): ResponseInterface> $middlewares
     * @param Closure $lastHandler
     * @psalm-param Closure(RequestInterface $request): ResponseInterface $lastHandler
     */
    public function __construct(
        private readonly array $middlewares,
        private readonly Closure $lastHandler,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        if (\count($this->middlewares) === 0) {
            return ($this->lastHandler)($request);
        }

        $handler = $this->lastHandler;
        foreach (\array_reverse($this->middlewares) as $middleware) {
            $handler = new MiddlewareHandler($handler, $middleware);
        }

        return $handler($request);
    }
}
