<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructors;

use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * PSR-based instructor
 */
class PsrInstructor implements InstructorInterface
{
    public function __construct(
        private readonly UriFactoryInterface $uriFactory,
        private readonly RequestInterface $baseRequest,
        private readonly ClientInterface $client,
        private readonly HttpProfiler $profiler,
    ) {
    }

    public function get(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('GET', $path, null, $version, $headers));
    }

    public function getJson(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('GET', $path, null, $version, $headers));
    }

    public function post(string $path, ?string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('POST', $path, $body, $version, $headers));
    }

    public function postJson(string $path, ?array $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('POST', $path, $body, $version, $headers));
    }

    public function head(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('HEAD', $path, null, $version, $headers));
    }

    public function headJson(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('HEAD', $path, null, $version, $headers));
    }

    public function put(string $path, ?string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('PUT', $path, $body, $version, $headers));
    }

    public function putJson(string $path, ?array $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('PUT', $path, $body, $version, $headers));
    }

    public function delete(string $path, ?string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('DELETE', $path, $body, $version, $headers));
    }

    public function deleteJson(string $path, ?array $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('DELETE', $path, $body, $version, $headers));
    }

    public function patch(string $path, ?string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest('PATCH', $path, $body, $version, $headers));
    }

    public function patchJson(string $path, ?array $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeJsonRequest('PATCH', $path, $body, $version, $headers));
    }

    public function request(RequestInterface $request): Response
    {
        $instruction = new HttpRequestInstruction(
            $this->client,
            $request,
        );
        $startMicrotime = \microtime(true);
        \assert(\is_float($startMicrotime));
        $instruction->execute();
        $endMicrotime = \microtime(true);
        \assert(\is_float($endMicrotime));
        $response = $instruction->getResponse();

        $result = $this->profiler->profile(
            $startMicrotime,
            $endMicrotime,
            $request,
            $response,
        );

        return new Response($response, $result);
    }

    public function waitSec(int|float $sec): void
    {
        $instruction = new WaitInstruction($sec);
        $instruction->execute();
    }

    /**
     * Makes JSON HTTP request
     *
     * @param non-empty-string $method
     * @param string $path
     * @param array|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return RequestInterface
     */
    protected function makeJsonRequest(
        string $method,
        string $path,
        array|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): RequestInterface {
        return $this->makeRequest(
            $method,
            $path,
            \is_null($body) ? null : \json_encode($body, \JSON_THROW_ON_ERROR),
            $version,
            $headers,
            !\is_null($body),
            true,
        );
    }

    /**
     * Makes HTTP request
     *
     * @param non-empty-string $method
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @param bool $jsonRequest
     * @param bool $acceptsJson
     * @return RequestInterface
     */
    protected function makeRequest(
        string $method,
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
        bool $jsonRequest = false,
        bool $acceptsJson = false,
    ): RequestInterface {
        $request = clone $this->baseRequest;
        if (!\is_null($body)) {
            $request->getBody()->write($body);
        }
        $request = $request->withMethod($method)
            ->withUri($this->makeUri($request->getUri(), $path))
            ->withProtocolVersion($version);

        if ($jsonRequest && !$request->hasHeader('Content-Type')) {
            $request = $request->withHeader('Content-Type', 'application/json; charset=UTF-8');
        }
        if ($acceptsJson) {
            $request = $request->withHeader('Accept', 'application/json; charset=UTF-8');
        }
        if (!$request->hasHeader('Content-Length')) {
            $request = $request->withHeader('Content-Length', \strval(\strlen($body ?? '')));
        }

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }

    /**
     * Makes new UriInterface using baseUri and path
     *
     * @param UriInterface $uri
     * @param string       $path
     * @return UriInterface
     */
    protected function makeUri(
        UriInterface $uri,
        string $path,
        ?string $username = null,
        ?string $password = null,
    ): UriInterface {
        $uri = $this
            ->uriFactory
            ->createUri(\sprintf('%s/%s', \rtrim($uri->__toString(), '/'), \ltrim($path, '/')));

        if (!\is_null($username)) {
            return $uri->withUserInfo($username, $password);
        }
        return $uri;
    }
}
