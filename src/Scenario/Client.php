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
 * Main client implementation with short-hand methods
 */
class Client implements ClientInterface
{
    public function __construct(
        protected readonly PsrClientInterface $client,
        protected readonly RequestBuilderInterface $baseBuilder,
    ) {
    }

    public function waitSec(int|float $sec): void
    {
        $this->waitMicroSec($sec * 1_000_000.0);
    }

    public function waitMilliSec(int|float $milliSec): void
    {
        $this->waitMicroSec($milliSec * 1_000.0);
    }

    public function waitMicroSec(int|float $microSec): void
    {
        if ($microSec <= 0) {
            // Do nothing
            return;
        }
        /** @var int<0, max> */
        $microSeconds = \intval(\round($microSec, 0));
        \usleep($microSeconds);
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

    public function get(string $path, ?array $query = null): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('GET')
            ->path($path);

        if (!\is_null($query)) {
            $builder->query($query);
        }

        return $this->send($builder->createRequest());
    }

    public function post(string $path, ?string $body = null): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('POST')
            ->path($path);

        if (!\is_null($body)) {
            $builder->body($body);
        }

        return $this->send($builder->createRequest());
    }

    public function postJson(string $path, array $body): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('POST')
            ->path($path)
            ->json($body);

        return $this->send($builder->createRequest());
    }

    public function put(string $path, ?string $body = null): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('PUT')
            ->path($path);

        if (!\is_null($body)) {
            $builder->body($body);
        }

        return $this->send($builder->createRequest());
    }

    public function putJson(string $path, array $body): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('PUT')
            ->path($path)
            ->json($body);

        return $this->send($builder->createRequest());
    }

    public function delete(string $path, ?string $body = null): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('DELETE')
            ->path($path);

        if (!\is_null($body)) {
            $builder->body($body);
        }

        return $this->send($builder->createRequest());
    }

    public function deleteJson(string $path, array $body): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('DELETE')
            ->path($path)
            ->json($body);

        return $this->send($builder->createRequest());
    }

    public function patch(string $path, ?string $body = null): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('PATCH')
            ->path($path);

        if (!\is_null($body)) {
            $builder->body($body);
        }

        return $this->send($builder->createRequest());
    }

    public function patchJson(string $path, array $body): AssertableResponseInterface
    {
        $builder = $this->with()
            ->method('PATCH')
            ->path($path)
            ->json($body);

        return $this->send($builder->createRequest());
    }
}
