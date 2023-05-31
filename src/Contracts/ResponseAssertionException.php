<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Emits when response assertion was failed
 *
 * @todo get failed scenario position from trace
 */
class ResponseAssertionException extends RuntimeException
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string $message Assertion Message
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
