<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor\Middlewares;

use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class WaitSendRequestMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly int|float $waitSec)
    {
    }

    public function handleRequest(RequestInterface $request, callable $next): mixed
    {
        return $next($request);
    }

    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next): mixed
    {
        /** @var mixed */
        $result = $next($request, $response);

        /** @var int<0, max> */
        $usec = \intval(\round(\abs($this->waitSec) * 1_000_000));

        \usleep($usec);

        return $result;
    }
}
