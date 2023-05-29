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
use Psr\Http\Message\RequestInterface;

/**
 * PSR-based instructor
 */
class PsrInstructor implements InstructorInterface
{
    /** @var InstructionInterface[] $instructions */
    private array $instructions = [];

    /**
     * Constructor
     *
     * @param RequestInterface $baseRequest Base request instance
     * @param ClientInterface  $client
     */
    public function __construct(
        private readonly RequestInterface $baseRequest,
        private readonly ClientInterface $client,
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

    public function postJson(string $path, array $body, string $version = '1.1', array $headers = []): Response
    {
        return $this->post(
            $path,
            \json_encode($body, \JSON_THROW_ON_ERROR),
            $version,
            [...$headers, ...['Accept' => 'application/json; charset=UTF-8']],
        );
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
        $request = clone $this->baseRequest;
        $request->getBody()->write($body ?? '');
        $request = $request->withMethod($method)
            ->withUri($request->getUri()->withPath($path))
            ->withProtocolVersion($version);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }

    /**
     * Executes HTTP request
     *
     * @param RequestInterface $request
     * @return Response
     */
    public function request(RequestInterface $request): Response
    {
        $instruction = new HttpRequestInstruction(
            $this->client,
            $request,
        );
        $instruction->execute();
        $response = $instruction->getResponse();
        $this->instructions[] = $instruction;

        return new Response($response);
    }

    public function waitSec(int|float $sec): void
    {
        $instruction = new WaitInstruction($sec);
        $instruction->execute();
        $this->instructions[] = $instruction;
    }
}
