<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\AssertableResponseInterface;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Contracts\RequestBuilderInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Main HTTP client implementation
 */
class Client implements ClientInterface
{
    public function __construct(
        protected readonly PsrClientInterface $client,
        protected readonly RequestBuilderInterface $baseBuilder,
    ) {
    }

    public function send(RequestInterface $request): AssertableResponseInterface
    {
        $response = $this->client->sendRequest($request);

        return new AssertableResponse($request, $response);
    }

    public function with(): RequestBuilderInterface
    {
        return clone $this->baseBuilder;
    }
}
