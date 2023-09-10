<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Heavyrain\Contracts\MiddlewareInterface;
use Psr\Http\Client\ClientInterface;

/**
 * HTTP client interface with middleware
 */
interface HttpClientInterface extends ClientInterface
{
    public function addMiddleware(MiddlewareInterface $middleware): void;
}
