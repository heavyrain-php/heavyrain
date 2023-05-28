<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use JsonSerializable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

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
     *   code: int|string
     * }
     */
    public readonly ?array $exception;

    /**
     * Curl result information
     *
     * @var array<array-key, mixed>|null
     */
    public readonly ?array $curlInfo;

    public function __construct(
        public readonly bool $successRequest,
        public readonly string $summary,
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
     *   code: int|string
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
        ];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'request' => $this->request,
            'response' => $this->response,
            'exception' => $this->exception,
            'curlInfo' => $this->curlInfo,
        ];
    }
}
