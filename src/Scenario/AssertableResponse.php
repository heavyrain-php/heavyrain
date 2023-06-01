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
     * Asserts expected value is false
     *
     * @param bool $expected
     * @param string $message
     * @return self
     */
    protected function assertFalse(bool $expected, string $message): self
    {
        return $this->assertTrue(!$expected, $message);
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
