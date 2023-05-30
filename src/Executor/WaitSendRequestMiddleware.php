<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class WaitSendRequestMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly int|float $waitSec)
    {
    }

    /** @psalm-suppress MissingReturnType */
    public function handleRequest(RequestInterface $request, callable $next)
    {
        return $next($request);
    }

    /** @psalm-suppress MissingReturnType */
    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        /** @var mixed */
        $result = $next($request, $response);

        /** @var int<0, max> */
        $usec = \intval(\round(\abs($this->waitSec) * 1_000_000));
        \usleep($usec);

        return $result;
    }
}
