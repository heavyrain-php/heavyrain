<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware interface
 */
interface MiddlewareInterface
{
    /**
     * Handles request and returns response
     *
     * @param RequestInterface $request
     * @param callable $next
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, callable $next): ResponseInterface;
}
