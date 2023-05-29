<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use JsonSerializable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Serializable HTTP result
 * data is stored as array to reduce memory comsumption
 */
final class HttpResult implements JsonSerializable
{
    /**
     * Request informarion
     *
     * @var array{
     *   protocolVersion: string,
     *   method: string,
     *   uri: string,
     *   path: string,
     *   headers: array<string[]>
     * }
     */
    public readonly array $request;

    /**
     * Response information
     *
     * @var null|array{
     *   statusCode: int,
     *   reasonPhrase: string,
     *   headers: array<string[]>
     * }
     */
    public readonly ?array $response;

    /**
     * Exception information
     *
     * @var null|array{
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     */
    public readonly ?array $exception;

    /**
     * Curl result information
     *
     * @var array<array-key, mixed>|null
     */
    public readonly ?array $curlInfo;

    /** @var bool True if assertion has completed */
    private bool $assertionCompleted = false;

    /** @var bool True if assertion has succeeded */
    private bool $assertionSucceeded = false;

    public function __construct(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
        ?array $curlInfo = null,
    ) {
        $this->request = $this->createRequestToArray($request);
        $this->response = $this->createResponseToArray($response);
        $this->exception = $this->createExceptionToArray($exception);
        $this->curlInfo = $curlInfo;
    }

    public function completeAssertion(bool $succeeded = true): void
    {
        $this->assertionCompleted = true;
        $this->assertionSucceeded = $succeeded;
    }

    public function isSucceeded(): bool
    {
        return $this->assertionCompleted && $this->assertionSucceeded;
    }

    /**
     * Creates request informarion
     *
     * @param RequestInterface $request
     * @return array{
     *   protocolVersion: string,
     *   method: string,
     *   uri: string,
     *   path: string,
     *   headers: array<string[]>
     * }
     */
    private static function createRequestToArray(RequestInterface $request): array
    {
        return [
            'protocolVersion' => $request->getProtocolVersion(),
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->__toString(),
            'path' => $request->getUri()->getPath(),
            'headers' => $request->getHeaders(),
        ];
    }

    /**
     * Creates response information
     *
     * @param ResponseInterface|null $response
     * @return null|array{
     *   statusCode: int,
     *   reasonPhrase: string,
     *   headers: array<string[]>
     * }
     */
    private static function createResponseToArray(?ResponseInterface $response): ?array
    {
        if (\is_null($response)) {
            return null;
        }

        return [
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
        ];
    }

    /**
     * Creates exception information
     *
     * @param Throwable|null $exception
     * @return null|array{
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     */
    private static function createExceptionToArray(?Throwable $exception): ?array
    {
        if (\is_null($exception)) {
            return null;
        }

        return [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'previousMessage' => $exception->getPrevious()?->getMessage(),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'request' => $this->request,
            'response' => $this->response,
            'exception' => $this->exception,
            'curlInfo' => $this->curlInfo,
            'assertionCompleted' => $this->assertionCompleted,
            'assertionSucceeded' => $this->assertionSucceeded,
        ];
    }
}
