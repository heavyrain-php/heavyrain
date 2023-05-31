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
     * @param string $target
     * @return self
     * @link https://www.php-fig.org/psr/psr-7/#14-request-targets-and-uris
     * @link https://httpwg.org/specs/rfc9112.html#request.target
     */
    public function requestTarget(string $target): self;

    /**
     * Set protocol version
     *
     * @param string $version
     * @psalm-param '0.9'|'1.0'|'1.1'|'2.0' $version
     * @phpstan-param '0.9'|'1.0'|'1.1'|'2.0' $version
     * @phan-param '0.9'|'1.0'|'1.1'|'2.0' $version
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
     * @psalm-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE' $method
     * @phpstan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE' $method
     * @phan-param 'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE' $method
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
     * @psalm-param non-empty-string $path
     * @phpstan-param non-empty-string $path
     * @phan-param non-empty-string $path
     * @return self
     */
    public function path(string $path): self;

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
     * @psalm-param array<string, int|float|non-empty-string> $query
     * @phpstan-param array<string, int|float|non-empty-string> $query
     * @phan-param array<string, int|float|non-empty-string> $query
     * @return self
     */
    public function query(array $query): self;

    /**
     * Set new UriInterface
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
     * @psalm-param array<non-empty-string, non-empty-string|non-empty-string[]> $headers
     * @phpstan-param array<non-empty-string, non-empty-string|non-empty-string[]> $headers
     * @phan-param array<non-empty-string, non-empty-string|non-empty-string[]> $headers
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
     * @param string|string[] $value
     * @psalm-param non-empty-string|non-empty-string[] $value
     * @phpstan-param non-empty-string|non-empty-string[] $value
     * @phan-param non-empty-string|non-empty-string[] $value
     * @return self
     */
    public function header(string $name, string|array $value): self;

    /**
     * Set JSON body
     *
     * @param array|null $json
     * @psalm-param array<array-key, mixed> $json
     * @phpstan-param array<array-key, mixed> $json
     * @phan-param array<array-key, mixed> $json
     * @param int|null $flags
     * @return self
     */
    public function json(?array $json = null, ?int $flags = null): self;

    /**
     * Set body
     *
     * @param string $body
     * @return self
     */
    public function body(string $body): self;
}
