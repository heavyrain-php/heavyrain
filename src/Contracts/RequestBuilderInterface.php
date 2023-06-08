<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Mutable HTTP Request builder
 *
 * @link https://www.php-fig.org/psr/psr-7/
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP
 * @link https://httpwg.org/specs/rfc9110.html
 * @link https://httpwg.org/specs/rfc9112.html
 */
interface RequestBuilderInterface
{
    /**
     * Creates and returns PSR-7 UriInterface
     *
     * @return UriInterface
     */
    public function createUri(): UriInterface;

    /**
     * Creates and returns PSR-7 RequestInterface
     *
     * @return RequestInterface
     */
    public function createRequest(): RequestInterface;

    /**
     * Set request target
     *
     * @defaults null
     * @param string|null $target
     * @return self
     * @link https://www.php-fig.org/psr/psr-7/#14-request-targets-and-uris
     * @link https://httpwg.org/specs/rfc9112.html#request.target
     */
    public function requestTarget(string|null $target): self;

    /**
     * Set protocol version
     *
     * @defaults '1.1'
     * @param string $version
     * @psalm-param '0.9'|'1.0'|'1.1'|'2' $version
     * @phpstan-param '0.9'|'1.0'|'1.1'|'2' $version
     * @phan-param '0.9'|'1.0'|'1.1'|'2' $version
     * @return self
     * @link https://httpwg.org/specs/rfc9112.html#http.version
     */
    public function protocolVersion(string $version): self;

    /**
     * Set HTTP request method
     *
     * It should be uppercase otherwise emits error when you use static analysis
     *
     * @param string $method
     * @psalm-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @phpstan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @phan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH' $method
     * @return self
     * @link https://httpwg.org/specs/rfc9110.html#methods
     */
    public function method(string $method): self;

    /**
     * Set URI user info
     *
     * @param string $username
     * @psalm-param non-empty-string $username
     * @phpstan-param non-empty-string $username
     * @phan-param non-empty-string $username
     * @param string|null $password
     * @psalm-param non-empty-string|null $password
     * @phpstan-param non-empty-string|null $password
     * @phan-param non-empty-string|null $password
     * @return self
     */
    public function userInfo(string $username, ?string $password = null): self;

    /**
     * Set URI path
     *
     * @param string $path
     * @return self
     */
    public function path(string $path): self;

    /**
     * Set URI args within path like /users/{userId}
     *
     * @param array $args
     * @psalm-param array<non-empty-string, scalar> $args
     * @phpstan-param array<non-empty-string, scalar> $args
     * @phan-param array<non-empty-string, scalar> $args
     * @return self
     */
    public function pathArgs(array $args): self;

    /**
     * Set URI fragment
     *
     * @param string $fragment
     * @return self
     */
    public function fragment(string $fragment): self;

    /**
     * Set URI query string
     *
     * @param array $query
     * @psalm-param array<non-empty-string, scalar> $query
     * @phpstan-param array<non-empty-string, scalar> $query
     * @phan-param array<non-empty-string, scalar> $query
     * @return self
     */
    public function query(array $query): self;

    /**
     * Set new UriInterface
     *
     * CAUTION: set Uri forcing to use this instance and other Uri parameters will be ignored
     *
     * @param UriInterface $uri
     * @return self
     */
    public function uri(UriInterface $uri): self;

    /**
     * Set header Authorization: Basic base64-encoded-string
     *
     * @param string $username
     * @psalm-param non-empty-string $username
     * @phpstan-param non-empty-string $username
     * @phan-param non-empty-string $username
     * @param string $password
     * @psalm-param non-empty-string $password
     * @phpstan-param non-empty-string $password
     * @phan-param non-empty-string $password
     * @return self
     */
    public function basic(string $username, string $password): self;

    /**
     * Set header Authorization: Bearer string
     *
     * @param string $token
     * @psalm-param non-empty-string $token
     * @phpstan-param non-empty-string $token
     * @phan-param non-empty-string $token
     * @return self
     */
    public function bearer(string $token): self;

    /**
     * Set header Authorization
     *
     * @param string $value
     * @psalm-param non-empty-string $value
     * @phpstan-param non-empty-string $value
     * @phan-param non-empty-string $value
     * @return self
     */
    public function authorization(string $value): self;

    /**
     * Set Content-Type: text/plain
     *
     * @return self
     */
    public function contentTypePlain(): self;

    /**
     * Set Content-Type: application/json; charset=UTF-8
     *
     * @param string|null $charset omits charset when null
     * @psalm-param non-empty-string|null $charset
     * @phpstan-param non-empty-string|null $charset
     * @phan-param non-empty-string|null $charset
     * @return self
     */
    public function contentTypeJson(?string $charset = 'UTF-8'): self;

    /**
     * Set header Content-Type
     *
     * @param string $contentType
     * @psalm-param non-empty-string $contentType
     * @phpstan-param non-empty-string $contentType
     * @phan-param non-empty-string $contentType
     * @return self
     */
    public function contentType(string $contentType): self;

    /**
     * Set header Accept: *\/*
     *
     * @return self
     */
    public function acceptAll(): self;

    /**
     * Set header Accept: text/html
     *
     * @return self
     */
    public function acceptHtml(): self;

    /**
     * Set header Accept: text/*
     *
     * @return self
     */
    public function acceptText(): self;

    /**
     * Set header Accept: application/json; charset=UTF-8
     *
     * @param string|null $charset omits charset when null
     * @psalm-param non-empty-string|null $charset
     * @phpstan-param non-empty-string|null $charset
     * @phan-param non-empty-string|null $charset
     * @return self
     */
    public function acceptJson(?string $charset = 'UTF-8'): self;

    /**
     * Set header Accept
     *
     * @param string|string[] $contentType
     * @psalm-param non-empty-string|non-empty-string[] $contentType
     * @phpstan-param non-empty-string|non-empty-string[] $contentType
     * @phan-param non-empty-string|non-empty-string[] $contentType
     * @return self
     */
    public function accept(string|array $contentType): self;

    /**
     * Set header User-Agent
     *
     * @param string $userAgent
     * @psalm-param non-empty-string $userAgent
     * @phpstan-param non-empty-string $userAgent
     * @phan-param non-empty-string $userAgent
     * @return self
     */
    public function userAgent(string $userAgent): self;

    /**
     * Set header list
     *
     * @param array $headers
     * @psalm-param array<non-empty-string, mixed> $headers
     * @phpstan-param array<non-empty-string, mixed> $headers
     * @phan-param array<non-empty-string, mixed> $headers
     * @return self
     */
    public function headers(array $headers): self;

    /**
     * Set header
     *
     * @param string $name
     * @psalm-param non-empty-string $name
     * @phpstan-param non-empty-string $name
     * @phan-param non-empty-string $name
     * @param mixed $value
     * @return self
     */
    public function header(string $name, mixed $value): self;

    /**
     * Set JSON body
     *
     * @param mixed $json
     * @param string|null $charset
     * @psalm-param non-empty-string|null $charset
     * @phpstan-param non-empty-string|null $charset
     * @phan-param non-empty-string|null $charset
     * @param int $flags
     * @param int $depth
     * @psalm-param int<1, 2147483647> $depth
     * @phpstan-param int<1, 2147483647> $depth
     * @phan-param int<1, 2147483647> $depth
     * @return self
     */
    public function json(mixed $json, ?string $charset = 'UTF-8', int $flags = 0, int $depth = 512): self;

    /**
     * Set body
     *
     * @param string $body
     * @return self
     */
    public function body(string $body): self;
}
