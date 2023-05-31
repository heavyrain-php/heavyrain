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
     * Returns RequestBuilder for detail request creation
     *
     * @return RequestBuilderInterface
     */
    public function builder(): RequestBuilderInterface;

    /**
     * Sends request and returns AssertableResponse
     *
     * @param RequestInterface $request
     * @return AssertableResponseInterface
     */
    public function send(RequestInterface $request): AssertableResponseInterface;

    /**
     * Shorthand request with GET
     *
     * @param string $path
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function get(string $path, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with HEAD
     *
     * @param string $path
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function head(string $path, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with OPTIONS
     *
     * @param string $path
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function options(string $path, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with POST
     *
     * @param string $path
     * @param string|null $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function post(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with POST JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function postJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with POST form
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function postForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PUT
     *
     * @param string $path
     * @param string|null $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function put(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PUT JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function putJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PUT form
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function putForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with DELETE
     *
     * @param string $path
     * @param string|null $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function delete(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with DELETE JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function deleteJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with DELETE form
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function deleteForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PATCH
     *
     * @param string $path
     * @param string|null $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function patch(string $path, ?string $body = null, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PATCH JSON
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function patchJson(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;

    /**
     * Shorthand request with PATCH form
     *
     * @param string $path
     * @param array $body
     * @psalm-param array<array-key, mixed> $body
     * @phpstan-param array<array-key, mixed> $body
     * @phan-param array<array-key, mixed> $body
     * @param bool $assertsOk Auto assertion which status code is 200-299
     * @return AssertableResponseInterface
     */
    public function patchForm(string $path, array $body, bool $assertsOk = true): AssertableResponseInterface;
}
