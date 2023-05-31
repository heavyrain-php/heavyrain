<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Assertable HTTP response object
 */
interface AssertableResponseInterface
{
    /**
     * Returns PSR-7 Request object
     *
     * @return RequestInterface
     */
    public function getRawRequest(): RequestInterface;

    /**
     * Returns PSR-7 Response object
     *
     * @return ResponseInterface
     */
    public function getRawResponse(): ResponseInterface;

    /**
     * Returns JSON body as array
     *
     * @param int $depth JSON depth
     * @psalm-param int<1, 2147483647> $depth
     * @phpstan-param int<1, 2147483647> $depth
     * @phan-param int<1, 2147483647> $depth
     * @param int $flags JSON flags
     * @return array
     * @throws ResponseAssertionException throws when Content-Type or body is not JSON
     */
    public function getJsonBody(int $depth = 512, int $flags = 0): array;

    /**
     * Returns body
     *
     * @return string|null returns null when body is empty
     */
    public function getBody(): ?string;

    /**
     * Asserts body is JSON and has key
     *
     * @param string|string[] $key key string or array
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertJsonHasKey(string|array $key): self;

    /**
     * Asserts body has expected string
     *
     * @param string $needle
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertBodyHas(string $needle): self;

    /**
     * Asserts Content-Type is HTML
     *
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertIsHtml(): self;

    /**
     * Asserts Content-Type and body is JSON
     *
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertIsJson(): self;

    /**
     * Asserts Content-Type response header
     *
     * @param string $contentType
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertContentType(string $contentType): self;

    /**
     * Asserts response header
     *
     * @param string $name
     * @param string $value
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertHeader(string $name, string $value): self;

    /**
     * Asserts status code is 200
     *
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertOk(): self;

    /**
     * Asserts status code
     *
     * @param int $code
     * @return self
     * @throws ResponseAssertionException
     */
    public function assertStatusCode(int $code): self;
}
