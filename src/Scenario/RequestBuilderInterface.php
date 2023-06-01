<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Heavyrain\Scenario\BodyEncodeException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Mutable HTTP Request builder implementation
 */
class RequestBuilder implements RequestBuilderInterface
{
    /**
     * @var string
     * @psalm-var non-empty-string
     * @phpstan-var non-empty-string
     * @phan-var non-empty-string
     */
    private string $method = 'GET';

    private ?string $requestTarget = null;

    private ?string $protocolVersion = null;

    private ?string $username = null;

    private ?string $password = null;

    private ?string $path = null;

    private ?string $fragment = null;

    private ?array $query = null;

    private ?UriInterface $uri = null;

    private ?array $headers = [];

    private ?string $body = null;

    /**
     * @param UriFactoryInterface $uriFactory
     * @param RequestFactoryInterface $requestFactory
     * @param string $baseUri http(s)://domain
     */
    public function __construct(
        protected readonly UriFactoryInterface $uriFactory,
        protected readonly RequestFactoryInterface $requestFactory,
        protected readonly string $baseUri,
    ) {
        \assert(\str_starts_with($this->baseUri, 'http'));
    }

    public function createUri(): UriInterface
    {
        // @todo implementation
        return $this->uriFactory->createUri($this->baseUri);
    }

    public function createRequest(): RequestInterface
    {
        // @todo implementation
        return $this->requestFactory->createRequest(
            $this->method,
            $this->createUri(),
        );
    }

    public function requestTarget(string $target): self
    {
        $this->requestTarget = $target;
        return $this;
    }

    public function protocolVersion(string $version): self
    {
        $this->protocolVersion = $version;
        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function userInfo(string $username, ?string $password = null): self
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function fragment(string $fragment): self
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function query(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function uri(UriInterface $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function basic(string $username, string $password): self
    {
        $this->authorization(\sprintf('Basic %s', \base64_encode(\sprintf('%s:%s', $username, $password))));
        return $this;
    }

    public function bearer(string $token): self
    {
        $this->authorization(\sprintf('Bearer %s', $token));
        return $this;
    }

    public function authorization(string $value): self
    {
        $this->header('Authorization', $value);
        return $this;
    }

    public function contentType(string $contentType): self
    {
        $this->header('Content-Type', $contentType);
        return $this;
    }

    public function accept(string|array $contentType): self
    {
        $this->header('Accept', $contentType);
        return $this;
    }

    public function userAgent(string $userAgent): self
    {
        $this->header('User-Agent', $userAgent);
        return $this;
    }

    public function headers(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
        return $this;
    }

    public function header(string $name, string|array $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json(?array $json = null, int $flags = 0, int $depth = 512): self
    {
        $result = \json_encode($json, $flags, $depth);

        if ($result === false) {
            throw new BodyEncodeException(
                \json_last_error_msg(),
                \json_last_error(),
                $json,
            );
        }

        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }
}
