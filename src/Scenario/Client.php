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
    ) {
    }

    public function builder(): RequestBuilderInterface
    {

    }

    public function send(RequestInterface $request): AssertableResponseInterface
    {
        $response = $this->client->sendRequest($request);
    }

    public function get(string $path, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function head(string $path, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function options(string $path, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function post(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function postJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function postForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function put(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function putJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function putForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function delete(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function deleteJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function deleteForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function patch(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function patchJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

    public function patchForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface
    {

    }

}
