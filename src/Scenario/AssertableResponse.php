<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\AssertableResponseInterface;
use Heavyrain\Contracts\ResponseAssertionExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Assertable response class
 */
class AssertableResponse implements AssertableResponseInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response,
    ) {
    }

    public function getRawRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getRawResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getJsonBody(int $depth = 512, int $flags = 0): array
    {
        $this->assertIsJson();

        $body = $this->getBody();

        if (\is_null($body)) {
            throw new ResponseAssertionException($this->request, $this->response, 'Response body is empty');
        }

        /** @var null|bool|array */
        $decoded = \json_decode($body, true, $depth, $flags);
        $jsonLastError = \json_last_error();
        $jsonLastErrorMsg = \json_last_error_msg();

        if (\is_null($decoded) || \is_bool($decoded) || $jsonLastError !== \JSON_ERROR_NONE) {
            throw new ResponseAssertionException(
                $this->request,
                $this->response,
                \sprintf('Failed to decode JSON body code:%d message:%s', $jsonLastError, $jsonLastErrorMsg),
            );
        }

        return $decoded;
    }

    public function getBody(): ?string
    {
        $body = (string)$this->response->getBody();

        if ($body === '') {
            return null;
        }

        return $body;
    }

    public function assertJsonHasKey(string|array $key): self
    {
        $json = $this->getJsonBody();

        $keys = \is_array($key) ? $key : [$key];

        /** @todo deep key by dot */
        foreach ($keys as $k) {
            $this->assertTrue(
                \array_key_exists($k, $json),
                \sprintf('json has key %s', $k),
            );
        }
        return $this;
    }

    public function assertBodyContains(string $needle): self
    {
        return $this->assertTrue(
            \str_contains($this->getbody() ?? '', $needle),
            \sprintf('body contains %s', $needle),
        );
    }

    public function assertIsHtml(): self
    {
        return $this->assertContentType('text/html');
    }

    public function assertIsJson(): self
    {
        return $this->assertContentType('application/json');
    }

    public function assertContentType(string $contentType): self
    {
        $separated = \explode(';', $this->response->getHeaderLine('Content-Type'), 2);

        return $this->assertHeaderHas('Content-Type')->assertTrue(
            $separated[0] === $contentType,
            \sprintf('header Content-Type is %s, got %s', $contentType, $separated[0]),
        );
    }

    public function assertHeader(string $name, string $value): self
    {
        return $this->assertHeaderHas($name)->assertTrue(
            $this->response->getHeaderLine($name) === $value,
            \sprintf('header %s is %s, got %s', $name, $value, $this->response->getHeaderLine($name)),
        );
    }

    public function assertHeaderHas(string $name): self
    {
        return $this->assertTrue(
            $this->response->hasHeader($name),
            \sprintf('header has %s', $name),
        );
    }

    public function assertOk(): self
    {
        $actual = $this->response->getStatusCode();
        return $this->assertTrue(
            $actual >= 200 && $actual <= 399,
            \sprintf('response code is 200-399, got %d', $actual),
        );
    }

    public function assertStatusCode(int $code): self
    {
        return $this->assertTrue(
            $this->response->getStatusCode() === $code,
            \sprintf('response status code is %d, got %d', $code, $this->response->getStatusCode()),
        );
    }

    /**
     * Asserts expected value is true
     *
     * @param bool $expected
     * @param string $message
     * @return self
     * @throws ResponseAssertionExceptionInterface emits when expected value is false
     */
    protected function assertTrue(bool $expected, string $message): self
    {
        if (!$expected) {
            throw new ResponseAssertionException($this->request, $this->response, $message);
        }
        return $this;
    }
}
