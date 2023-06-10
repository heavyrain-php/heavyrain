<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use JsonSerializable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Stringable;
use Throwable;

/**
 * Serializable HTTP result
 * data is stored as array to reduce memory comsumption
 */
final class HttpResult implements JsonSerializable, Stringable
{
    private const CURL_INFO_HEADER = '__curl_info';

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
     *   bodyLength: int,
     *   headers: array<string[]>
     * }
     */
    public readonly ?array $response;

    /**
     * Exception information
     *
     * @var null|array{
     *   name: string,
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     */
    private ?array $exception;

    /**
     * Curl result information
     *
     * @var array<array-key, mixed>|null
     */
    public readonly ?array $curlInfo;

    public function __construct(
        public readonly float $startMicrotime,
        public readonly float $endMicrotime,
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ) {
        $this->request = self::createRequestToArray($request);
        $this->response = self::createResponseToArray($response);
        $this->exception = self::createExceptionToArray($exception);
        $this->curlInfo = self::convertCurlInfo($response);
    }

    public function getException(): ?array
    {
        return $this->exception;
    }

    public function setException(Throwable $exception): void
    {
        $this->exception = self::createExceptionToArray($exception);
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
     *   bodyLength: int,
     *   headers: array<string[]>
     * }
     */
    private static function createResponseToArray(?ResponseInterface $response): ?array
    {
        if (\is_null($response)) {
            return null;
        }

        $body = $response->getBody()->__toString();

        return [
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'bodyLength' => \function_exists('mb_strlen') ? \mb_strlen($body) : \strlen($body),
            'headers' => $response->getHeaders(),
        ];
    }

    /**
     * Creates exception information
     *
     * @param Throwable|null $exception
     * @return null|array{
     *   name: string,
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
            'name' => \get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'previousMessage' => $exception->getPrevious()?->getMessage(),
        ];
    }

    /**
     * Get Curl information from Response header
     *
     * @param ResponseInterface|null $response
     * @return array|null
     */
    private static function convertCurlInfo(?ResponseInterface $response): ?array
    {
        if (\is_null($response) || !$response->hasHeader(self::CURL_INFO_HEADER)) {
            return null;
        }

        $info = \json_decode(
            $response->getHeaderLine(self::CURL_INFO_HEADER),
            true,
            512,
            \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE,
        );

        \assert(\is_array($info));

        return $info;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'startMicrotime' => $this->startMicrotime,
            'endMicrotime' => $this->endMicrotime,
            'request' => $this->request,
            'response' => $this->response,
            'exception' => $this->exception,
            'curlInfo' => $this->curlInfo,
        ];
    }

    public function __toString(): string
    {
        $prefix = \sprintf('%s %s', $this->request['method'], $this->request['path']);

        if (!\is_null($this->exception)) {
            return \sprintf('%s: Exception: %s %s', $prefix, $this->exception['name'], $this->exception['message']);
        }
        \assert(!\is_null($this->response));
        return \sprintf(
            '%s: Succeded: %s %s',
            $prefix,
            $this->response['statusCode'],
            $this->response['reasonPhrase'],
        );
    }
}
