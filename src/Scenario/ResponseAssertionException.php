<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\ResponseAssertionExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Emits when response assertion was failed
 */
class ResponseAssertionException extends RuntimeException implements ResponseAssertionExceptionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response,
        string $message,
    ) {
        parent::__construct(\sprintf('Failed to assert %s', $message));
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
