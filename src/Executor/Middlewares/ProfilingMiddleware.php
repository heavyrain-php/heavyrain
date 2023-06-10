<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor\Middlewares;

use Buzz\Middleware\MiddlewareInterface;
use Heavyrain\Scenario\HttpProfiler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ProfilingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly HttpProfiler $profiler,
    ) {
    }

    public function handleRequest(RequestInterface $request, callable $next): mixed
    {
        return $next($request->withHeader('Heavyrain-Start', \strval(\microtime(true))));
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): mixed
    {
        if (!$request->hasHeader('Heavyrain-Start')) {
            throw new \RuntimeException('Undefined request header Heavyrain-Start');
        }

        $this->profiler->profile(
            \floatval($request->getHeaderLine('Heavyrain-Start')),
            \microtime(true),
            $request,
            $response,
        );

        return $next($request, $response);
    }
}
