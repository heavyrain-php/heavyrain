<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Heavyrain\HttpClient;

use Heavyrain\Contracts\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 */
final class MiddlewareHandler
{
    /**
     * @param MiddlewareHandler|callable(RequestInterface $request): ResponseInterface $handler
     * @param null|MiddlewareInterface|callable(RequestInterface $request, callable $next): ResponseInterface $middleware
     */
    public function __construct(
        private readonly MiddlewareHandler|callable $handler,
        private readonly null|MiddlewareInterface|callable $middleware = null,
    ) {
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        if (\is_null($this->middleware)) {
            return ($this->handler)($request);
        }

        if (\is_callable($this->middleware)) {
            return ($this->middleware)($request, $this->handler);
        }

        return $this->middleware->process($request, $this->handler);
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }
}
