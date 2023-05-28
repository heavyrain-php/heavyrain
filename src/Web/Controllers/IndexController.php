<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Web\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Healthcheck
 */
final class IndexController
{
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = '{"ok":true}';
        $response->getBody()->write($body);
        return $response->withAddedHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withAddedHeader('Content-Length', \strval(\strlen($body)));
    }
}
