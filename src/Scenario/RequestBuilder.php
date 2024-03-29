<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\RequestBuilderInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
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
     */
    private string $method = 'GET';

    /**
     * @var string|null
     */
    private ?string $requestTarget = null;

    /**
     * @var string|null
     * @psalm-var non-empty-string|null
     */
    private ?string $protocolVersion = null;

    /**
     * @var string|null
     * @psalm-var non-empty-string|null
     */
    private ?string $username = null;

    /**
     * @var string|null
     * @psalm-var non-empty-string|null
     */
    private ?string $password = null;

    /**
     * @var string|null
     */
    private ?string $path = null;

    /**
     * @var array|null
     * @psalm-var array<non-empty-string, scalar>|null
     */
    private ?array $pathArgs = null;

    /**
     * @var string|null
     */
    private ?string $fragment = null;

    /**
     * @var array|null
     * @psalm-var array<non-empty-string, string>
     */
    private ?array $query = null;

    /**
     * @var UriInterface|null
     */
    private ?UriInterface $uri = null;

    /**
     * @var array
     * @psalm-var array<non-empty-string, mixed>
     */
    private array $headers = [];

    /**
     * @var string|null
     */
    private ?string $body = null;

    /**
     * @param UriFactoryInterface $uriFactory
     * @param RequestFactoryInterface $requestFactory
     * @param string $baseUri http(s)://domain
     */
    public function __construct(
        protected readonly UriFactoryInterface $uriFactory,
        protected readonly StreamFactoryInterface $streamFactory,
        protected readonly RequestFactoryInterface $requestFactory,
        protected readonly string $baseUri,
    ) {
        if (!\str_starts_with($this->baseUri, 'http://') && !\str_starts_with($this->baseUri, 'https://')) {
            throw new RequestBuilderException('baseUri must be http or https scheme');
        }
    }

    public function createUri(): UriInterface
    {
        if (!\is_null($this->uri)) {
            return $this->uri;
        }

        $uri = $this->uriFactory->createUri(\rtrim($this->baseUri, '/'));

        if (!\is_null($this->path)) {
            $uri = $uri->withPath($this->createPath());
        }
        if (!\is_null($this->query)) {
            $uri = $uri->withQuery($this->createQuery($this->query));
        }
        if (!\is_null($this->username)) {
            $uri = $uri->withUserInfo($this->username, $this->password);
        }
        if (!\is_null($this->fragment)) {
            $uri = $uri->withFragment($this->fragment);
        }

        return $uri;
    }

    public function createRequest(): RequestInterface
    {
        $request = $this->requestFactory->createRequest(
            $this->method,
            $this->createUri(),
        );

        if (!\is_null($this->requestTarget)) {
            $request = $request->withRequestTarget($this->requestTarget);
        }
        if (!\is_null($this->protocolVersion)) {
            $request = $request->withProtocolVersion($this->protocolVersion);
        }
        /** @var mixed $value */
        foreach ($this->headers as $name => $value) {
            $request = $request->withAddedHeader($name, $this->createHeaderValue($name, $value));
        }
        if (!\is_null($this->body)) {
            $request = $request->withBody($this->streamFactory->createStream($this->body));
        }

        return $request;
    }

    public function requestTarget(string|null $target): self
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
        $this->method = \strtoupper($method);
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

    public function pathArgs(array $args): self
    {
        $this->pathArgs = $args;
        return $this;
    }

    public function fragment(string $fragment): self
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function query(array $query): self
    {
        // TODO: support multi-dimentional array query
        foreach ($query as $name => $value) {
            $this->query[$name] = \strval($value);
        }
        return $this;
    }

    public function uri(UriInterface $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function basic(string $username, string $password): self
    {
        return $this->authorization(\sprintf('Basic %s', \base64_encode(\sprintf('%s:%s', $username, $password))));
    }

    public function bearer(string $token): self
    {
        return $this->authorization(\sprintf('Bearer %s', $token));
    }

    public function authorization(string $value): self
    {
        return $this->header('Authorization', $value);
    }

    public function contentTypePlain(): RequestBuilderInterface
    {
        return $this->contentType('text/plain');
    }

    public function contentTypeJson(?string $charset = 'UTF-8'): RequestBuilderInterface
    {
        if (\is_null($charset)) {
            return $this->contentType('application/json');
        }
        return $this->contentType('application/json; charset=' . $charset);
    }

    public function contentType(string $contentType): self
    {
        return $this->header('Content-Type', $contentType);
    }

    public function acceptAll(): RequestBuilderInterface
    {
        return $this->accept('*/*');
    }

    public function acceptText(): RequestBuilderInterface
    {
        return $this->accept('text/*');
    }

    public function acceptHtml(): RequestBuilderInterface
    {
        return $this->accept('text/html');
    }

    public function acceptJson(?string $charset = 'UTF-8'): RequestBuilderInterface
    {
        if (\is_null($charset)) {
            return $this->accept('application/json');
        }
        return $this->accept('application/json; charset=' . $charset);
    }

    public function accept(string|array $contentType): self
    {
        return $this->header('Accept', $contentType);
    }

    public function userAgent(string $userAgent): self
    {
        return $this->header('User-Agent', $userAgent);
    }

    public function headers(array $headers): self
    {
        /** @var mixed $value */
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
        return $this;
    }

    public function header(string $name, mixed $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json(mixed $json, ?string $charset = 'UTF-8', int $flags = 0, int $depth = 512): RequestBuilderInterface
    {
        $result = \json_encode($json, $flags | \JSON_THROW_ON_ERROR, $depth);
        \assert($result !== false);

        return $this->body($result)->contentTypeJson($charset)->acceptJson($charset);
    }

    public function body(string $body): self
    {
        $this->body = $body;
        $this->headers([
            'Content-Length' => \function_exists('mb_strlen') ? \mb_strlen($body) : \strlen($body),
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
        return $this;
    }

    /**
     * Creates path with args
     *
     * @return string
     */
    private function createPath(): string
    {
        $path = '/' . (\is_null($this->path) ? '' : \ltrim($this->path, '/'));

        // Add Path-Tag header for aggregation
        $this->header('Path-Tag', $path);

        if (!\is_null($this->pathArgs)) {
            // replace path args
            foreach ($this->pathArgs as $name => $value) {
                $search = \sprintf('{%s}', $name);
                if (!\str_contains($path, $search)) {
                    throw new RequestBuilderException(
                        \sprintf('pathArg %s is not defined in path=%s', $search, $path),
                    );
                }
                $path = \str_replace($search, \strval($value), $path);
            }
        }

        return $path;
    }

    /**
     * Creates query string
     *
     * @param array $query
     * @psalm-param array<string, scalar|\Stringable> $query
     * @return string
     */
    private function createQuery(array $query): string
    {
        /** @var string[] */
        $entries = [];

        foreach ($query as $name => $value) {
            $entries[] = \implode('=', [$name, $value]);
        }

        return \implode('&', $entries);
    }

    /**
     * Creates header value
     *
     * @param string $name
     * @param mixed $value
     * @return string|array
     * @psalm-return string|string[]
     * @throws \Heavyrain\Contracts\RequestBuilderExceptionInterface emits when invalid header value
     */
    private function createHeaderValue(string $name, mixed $value): string|array
    {
        if (\is_array($value)) {
            /** @var string[] */
            $results = [];
            foreach ($value as $v) {
                if (!\is_scalar($v)) {
                    throw new RequestBuilderException(
                        \sprintf('%s is invalid value type=%s', $name, \gettype($v)),
                    );
                }
                $results[] = \strval($v);
            }
            return $results;
        } elseif (!\is_scalar($value)) {
            throw new RequestBuilderException(
                \sprintf('%s is invalid value type=%s', $name, \gettype($value)),
            );
        }
        return \strval($value);
    }
}
