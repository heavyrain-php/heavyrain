<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Heavyrain\HttpClient;

use Heavyrain\Contracts\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareQueue
{
    /**
     * @param MiddlewareInterface[] $middlewares
     * @param callable $lastHandler
     * @psalm-param callable(RequestInterface $request): ResponseInterface $lastHandler
     */
    public function __construct(
        private readonly array $middlewares,
        private readonly callable $lastHandler,
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
