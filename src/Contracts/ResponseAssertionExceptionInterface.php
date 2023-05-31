<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Emits when response assertion was failed
 *
 * @todo get failed scenario position from trace
 */
interface ResponseAssertionExceptionInterface extends Throwable
{
    /**
     * @return RequestInterface returns PSR-7 RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @return ResponseInterface returns PSR-7 ResponseInterface
     */
    public function getResponse(): ResponseInterface;
}
