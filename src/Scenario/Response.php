<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\ResponseInterface;

/**
 * Assertable HTTP response
 */
class Response
{
    /** @var ?array $cachedJson previously decoded json array */
    private ?array $cachedJson = null;

    public function __construct(
        private readonly ResponseInterface $rawResponse,
        private readonly HttpResult $result,
    ) {
    }

    /**
     * Asserts and get json body
     *
     * @param int<1, 2147483647> $depth
     * @param int $flags
     * @return array
     */
    public function getJson(int $depth = 512, int $flags = 0): array
    {
        if (!\is_null($this->cachedJson)) {
            return $this->cachedJson;
        }
        $this->assertIsJson();
        $bodyStr = (string)$this->rawResponse->getBody();
        assert($depth > 0 && $depth < 2147483648);
        $decodedJson = \json_decode($bodyStr, true, $depth, $flags);
        $this->assertTrue(
            \json_last_error() === \JSON_ERROR_NONE,
            \sprintf('Failed to decode json message=%s', \json_last_error_msg()),
        );
        \assert(\is_array($decodedJson));
        $this->cachedJson = $decodedJson;
        return $this->cachedJson;
    }

    public function getRawResponse(): ResponseInterface
    {
        return $this->rawResponse;
    }

    public function assertJsonHas(string $key, mixed $expected): self
    {
        $this->assertJsonHasKey($key);
        $json = $this->getJson();
        \assert(\is_float($expected) || \is_int($expected) || \is_string($expected));
        $this->assertTrue(
            $json[$key] === $expected,
            \sprintf('Invalid JSON key=%s value=%s expected=%s', $key, \json_encode($json[$key], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), $expected),
        );
        return $this;
    }

    public function assertJsonHasKey(string $key): self
    {
        $json = $this->getJson();
        $this->assertTrue(
            \array_key_exists($key, $json),
            \sprintf('Undefined JSON key=%s', $key),
        );
        return $this;
    }

    public function assertContentHas(string $expected): self
    {
        $bodyStr = (string)$this->rawResponse->getBody();
        $this->assertTrue(
            \str_contains($bodyStr, $expected),
            \sprintf('Failed to find %s in body', $expected),
        );
        return $this;
    }

    public function assertIsJson(): self
    {
        return $this->assertContentType([
            'application/json',
            'application/json; charset=utf-8',
            'application/json; charset=UTF-8',
            'application/json;charset=utf-8',
            'application/json;charset=UTF-8',
        ]);
    }

    /**
     * Asserts Content-Type header has expected
     *
     * @param string|string[] $expected
     * @return self
     */
    public function assertContentType(string|array $expected): self
    {
        return $this->assertHeaderHas('Content-Type', $expected);
    }

    /**
     * Asserts header has expected value includes one of valueList
     *
     * @param string $expectedName
     * @param string|string[] $expectedValue
     * @return self
     */
    public function assertHeaderHas(string $expectedName, string|array $expectedValue): self
    {
        $expectedValueList = \is_array($expectedValue) ? $expectedValue : [$expectedValue];
        $header = $this->rawResponse->getHeaderLine($expectedName);
        $message = \sprintf(
            'Header %s should be %s, actual %s',
            $expectedName,
            \implode(' or ', $expectedValueList),
            $header,
        );

        $exists = false;
        foreach ($expectedValueList as $v) {
            $exists = $exists || $header === $v;
        }
        return $this->assertTrue($exists, $message);
    }

    public function assertOk(): self
    {
        return $this->assertStatusCode(200);
    }

    public function assertStatusCode(int $expected): self
    {
        return $this->assertValid()->assertTrue(
            $expected === $this->rawResponse->getStatusCode(),
            \sprintf('Status code should be %d, actual %d', $expected, $this->rawResponse->getStatusCode()),
        );
    }

    public function assertValid(): self
    {
        return $this->assertTrue(
            !\is_null($this->rawResponse->getStatusCode()) && $this->rawResponse->getStatusCode() !== 0,
            'Response should be valid',
        );
    }

    protected function assertFalse(bool $expected, string $message): self
    {
        return $this->assertTrue(!$expected, $message);
    }

    /**
     * Asserts true and throws ResponseAssertionException when false.
     *
     * @param bool $expected
     * @param string $message Assertion message
     * @return self
     * @throws ResponseAssertionException
     */
    protected function assertTrue(bool $expected, string $message): self
    {
        $this->result->completeAssertion($expected);
        if (!$expected) {
            throw new ResponseAssertionException($this->rawResponse, $message);
        }
        return $this;
    }
}
