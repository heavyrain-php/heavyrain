<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructors;

use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\InstructionInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PSR-based instructor
 */
class PsrInstructor implements InstructorInterface
{
    /** @var InstructionInterface[] $instructions */
    private array $instructions = [];

    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ClientInterface $client,
        private readonly string $baseUri,
    ) {
    }

    /**
     * Retrieves instructions to execute
     *
     * @return InstructionInterface[]
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    public function get(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'GET',
            $path,
            null,
            $version,
            $headers,
        ));
    }

    public function post(string $path, null|string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'POST',
            $path,
            $body,
            $version,
            $headers,
        ));
    }

    public function head(string $path, null|string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'PUT',
            $path,
            $body,
            $version,
            $headers,
        ));
    }

    public function put(string $path, null|string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'PUT',
            $path,
            $body,
            $version,
            $headers,
        ));
    }

    public function delete(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'DELETE',
            $path,
            null,
            $version,
            $headers,
        ));
    }

    public function options(string $path, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'OPTIONS',
            $path,
            null,
            $version,
            $headers,
        ));
    }

    public function patch(string $path, null|string $body = null, string $version = '1.1', array $headers = []): Response
    {
        return $this->request($this->makeRequest(
            'PATCH',
            $path,
            $body,
            $version,
            $headers,
        ));
    }

    /**
     * Makes HTTP request
     *
     * @param non-empty-string $method
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return RequestInterface
     */
    protected function makeRequest(
        string $method,
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): RequestInterface {
        $stream = $body instanceof StreamInterface ? $body : $this->streamFactory->createStream($body ?? '');
        $request = $this->requestFactory
            ->createRequest(\strtoupper($method), \sprintf('%s/%s', $this->baseUri, \ltrim($path, '/')))
            ->withBody($stream)
            ->withProtocolVersion($version);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * Executes HTTP request
     *
     * @param RequestInterface $request
     * @return Response
     */
    protected function request(RequestInterface $request): Response
    {
        $this->instructions[] = new HttpRequestInstruction($request);

        return new Response($this->client->sendRequest($request));
    }

    public function waitSec(int|float $sec): void
    {
        $this->instructions[] = new WaitInstruction($sec);

        // TODO: to async

        /** @var int<0, max> $microsec */
        $microsec = \intval(\abs(\round($sec * 1000.0 * 1000.0, 0)));
        usleep($microsec);
    }
}
