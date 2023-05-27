<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Emits when response assertion was failed
 */
class ResponseAssertionException extends RuntimeException
{
    public function __construct(
        private readonly ResponseInterface $response,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
