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
     * @throws ResponseAssertionExceptionInterface throws when Content-Type or body is not JSON
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
     * @param string|string[] $key key string or string[]
     * @psalm-param non-empty-string|non-empty-string[] $key
     * @phpstan-param non-empty-string|non-empty-string[] $key
     * @phan-param non-empty-string|non-empty-string[] $key
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertJsonHasKey(string|array $key): self;

    /**
     * Asserts body has expected string
     *
     * @param string $needle
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertBodyHas(string $needle): self;

    /**
     * Asserts Content-Type is HTML
     *
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertIsHtml(): self;

    /**
     * Asserts Content-Type and body is JSON
     *
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertIsJson(): self;

    /**
     * Asserts Content-Type response header
     *
     * @param string $contentType case-insensitive Content-Type value
     * @psalm-param non-empty-string $contentType
     * @phpstan-param non-empty-string $contentType
     * @phan-param non-empty-string $contentType
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertContentType(string $contentType): self;

    /**
     * Asserts response header
     *
     * @param string $name case-insensitive header name
     * @psalm-param non-empty-string $name
     * @phpstan-param non-empty-string $name
     * @phan-param non-empty-string $name
     * @param string $value case-insensitive header value
     * @psalm-param non-empty-string $value
     * @phpstan-param non-empty-string $value
     * @phan-param non-empty-string $value
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertHeader(string $name, string $value): self;

    /**
     * Asserts status code is 200-299
     *
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertOk(): self;

    /**
     * Asserts status code
     *
     * @param int $code
     * @psalm-param int<100, 599> $code
     * @phpstan-param int<100, 599> $code
     * @phan-param int<100, 599> $code
     * @return self
     * @throws ResponseAssertionExceptionInterface
     */
    public function assertStatusCode(int $code): self;
}
