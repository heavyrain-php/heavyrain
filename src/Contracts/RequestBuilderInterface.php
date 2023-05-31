<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Mutable HTTP request object
 */
interface RequestBuilderInterface
{
    /**
     * Creates and returns PSR-7 RequestInterface
     *
     * @return RequestInterface
     */
    public function createRequest(): RequestInterface;

    public function get(): RequestInterface;
    public function head(): RequestInterface;
    public function post(): RequestInterface;
    public function put(): RequestInterface;
    public function delete(): RequestInterface;
    public function options(): RequestInterface;
    public function patch(): RequestInterface;

    public function requestTarget(string $target): self;
    public function protocolVersion(string $version): self;
    public function method(string $method): self;
    public function userInfo(string $username, ?string $password = null): self;
    public function path(string $path): self;
    public function fragment(string $fragment): self;
    public function query(array $query): self;
    public function uri(UriInterface $uri): self;
    public function basic(string $username, string $password): self;
    public function bearer(string $token): self;
    public function authorization(string $value): self;
    public function contentType(string $contentType): self;
    public function accept(string|array $contentType): self;
    public function userAgent(string $userAgent): self;
    public function headers(array $headers): self;
    public function header(string $name, string|array $value): self;
    public function json(?array $json = null, ?int $flags = null): self;
    public function body(string $body): self;
}
