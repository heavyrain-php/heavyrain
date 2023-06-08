<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Main HTTP client
 */
interface ClientInterface
{
    /**
     * Waits seconds
     *
     * @param int|float $sec
     * @return void
     */
    public function waitSec(int|float $sec): void;

    /**
     * Waits milli seconds
     *
     * @param int|float $milliSec
     * @return void
     */
    public function waitMilliSec(int|float $milliSec): void;

    /**
     * Waits micro seconds
     *
     * @param int|float $microSec
     * @return void
     */
    public function waitMicroSec(int|float $microSec): void;

    /**
     * Sends request and returns AssertableResponse
     *
     * @param RequestInterface $request
     * @return AssertableResponseInterface
     */
    public function send(RequestInterface $request): AssertableResponseInterface;

    /**
     * Returns RequestBuilder for detail request creation
     *
     * @return RequestBuilderInterface
     */
    public function with(): RequestBuilderInterface;

    /**
     * Requests with detail options using array shape
     *
     * @param string $method
     * @psalm-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @phpstan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @phan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @param string $path
     * @param array|null $pathArgs
     * @psalm-param array<non-empty-string, scalar>|null $pathArgs
     * @phpstan-param array<non-empty-string, scalar>|null $pathArgs
     * @phan-param array<non-empty-string, scalar>|null $pathArgs
     * @param array|null $query
     * @psalm-param array<non-empty-string, scalar>|null $query
     * @phpstan-param array<non-empty-string, scalar>|null $query
     * @phan-param array<non-empty-string, scalar>|null $query
     * @param string|null $body
     * @param array|null $json
     * @param bool $assertsOk
     * @return AssertableResponseInterface
     */
    public function requestWithOptions(
        string $method,
        string $path,
        ?array $pathArgs = null,
        ?array $query = null,
        ?string $body = null,
        ?array $json = null,
        bool $assertsOk = true,
    ): AssertableResponseInterface;

    /**
     * Shorthand request with GET
     *
     * @param string $path
     * @param array|null $query
     * @psalm-param array<non-empty-string, scalar> $query
     * @phpstan-param array<non-empty-string, scalar> $query
     * @phan-param array<non-empty-string, scalar> $query
     * @return AssertableResponseInterface
     */
    public function get(string $path, ?array $query = null): AssertableResponseInterface;

    /**
     * Shorthand request with GET JSON
     *
     * @param string $path
     * @param array|null $query
     * @psalm-param array<non-empty-string, scalar> $query
     * @phpstan-param array<non-empty-string, scalar> $query
     * @phan-param array<non-empty-string, scalar> $query
     * @return AssertableResponseInterface
     */
    public function getJson(string $path, ?array $query = null): AssertableResponseInterface;

    /**
     * Shorthand request with POST
     *
     * @param string $path
     * @param string|null $body
     * @return AssertableResponseInterface
     */
    public function post(string $path, ?string $body = null): AssertableResponseInterface;

    /**
     * Shorthand request with POST JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @return AssertableResponseInterface
     */
    public function postJson(string $path, array $body): AssertableResponseInterface;

    /**
     * Shorthand request with PUT
     *
     * @param string $path
     * @param string|null $body
     * @return AssertableResponseInterface
     */
    public function put(string $path, ?string $body = null): AssertableResponseInterface;

    /**
     * Shorthand request with PUT JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @return AssertableResponseInterface
     */
    public function putJson(string $path, array $body): AssertableResponseInterface;

    /**
     * Shorthand request with DELETE
     *
     * @param string $path
     * @param string|null $body
     * @return AssertableResponseInterface
     */
    public function delete(string $path, ?string $body = null): AssertableResponseInterface;

    /**
     * Shorthand request with DELETE JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @return AssertableResponseInterface
     */
    public function deleteJson(string $path, array $body): AssertableResponseInterface;

    /**
     * Shorthand request with PATCH
     *
     * @param string $path
     * @param string|null $body
     * @return AssertableResponseInterface
     */
    public function patch(string $path, ?string $body = null): AssertableResponseInterface;

    /**
     * Shorthand request with PATCH JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @return AssertableResponseInterface
     */
    public function patchJson(string $path, array $body): AssertableResponseInterface;
}
