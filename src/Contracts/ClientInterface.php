<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

/**
 * HTTP request client
 */
interface ClientInterface
{
    public function send(RequestBuilderInterface $request): AssertableResponseInterface;
}
