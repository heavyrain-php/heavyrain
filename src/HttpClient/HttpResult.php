<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use ArrayAccess;
use JsonSerializable;
use Stringable;
use Throwable;

/**
 * HTTP profiling result
 * for reduce memory and reference leaks, all data is stored in array
 * @template-implements ArrayAccess<string, array>
 */
final class HttpResult implements ArrayAccess, JsonSerializable, Stringable
{
    public const START_KEY = 'start';
    public const CONNECT_KEY = 'connect';
    public const SEND_START_KEY = 'send_start';
    public const SEND_END_KEY = 'send_end';
    public const RECEIVE_START_KEY = 'receive_start';
    public const RECEIVE_END_KEY = 'receive_end';

    public readonly array $result;

    public function __construct(
        ?Request $request,
        ?Response $response = null,
        ?Throwable $requestException = null,
        ?Throwable $uncaughtException = null,
    ) {
        $this->result = [
            'request' => self::convertRequest($request),
            'response' => self::convertResponse($response),
            'requestException' => self::convertRequestException($request, $requestException),
            'uncaughtException' => self::convertUncaughtException($uncaughtException),
        ];
    }

    public function offsetExists(mixed $offset): bool
    {
        return \in_array($offset, ['request', 'response', 'request_exception', 'uncaught_exception'], true);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!\array_key_exists($offset, $this->result)) {
            throw new \RuntimeException('invalid offset. supported: request, response, request_exception, uncaught_exception');
        }
        return $this->result[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('cannot set result after created');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('cannot unset result after created');
    }

    public function jsonSerialize(): mixed
    {
        return $this->result;
    }

    public function __toString(): string
    {
        return \json_encode($this->result, JSON_THROW_ON_ERROR);
    }

    /**
     * Conversion request to array
     *
     * @param Request|null $request
     * @return array|null
     */
    private static function convertRequest(?Request $request): array|null
    {
        if (\is_null($request)) {
            return null;
        }

        return [
            'protocolVersion' => $request->getProtocolVersions()[0],
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'pathTag' => $request->hasHeader('Path-Tag') ? $request->getHeader('Path-Tag') : $request->getUri()->getPath(),
            'size' => $request->getBody()->getContentLength(),
            'headers' => $request->getHeaders(),
        ];
    }

    /**
     * Conversion response to array
     *
     * @param Response|null $response
     * @return array|null
     */
    private static function convertResponse(?Response $response): array|null
    {
        if (\is_null($response)) {
            return null;
        }

        return [
            'statusCode' => $response->getStatus(),
            'reasonPhrase' => $response->getReason(),
            'size' => -1,
            'timing' => [
                'start' => $response->getRequest()->getAttribute(self::START_KEY),
                'connect' => $response->getRequest()->getAttribute(self::CONNECT_KEY),
                'sendStart' => $response->getRequest()->getAttribute(self::SEND_START_KEY),
                'sendEnd' => $response->getRequest()->getAttribute(self::SEND_END_KEY),
                'receiveStart' => $response->getRequest()->getAttribute(self::RECEIVE_START_KEY),
                'receiveEnd' => $response->getRequest()->getAttribute(self::RECEIVE_END_KEY),
            ],
            'headers' => $response->getHeaders(),
        ];
    }

    /**
     * Conversion request exception to array
     *
     * @param Request|null $request
     * @param Throwable|null $requestException
     * @return array|null
     */
    private static function convertRequestException(?Request $request, ?Throwable $requestException): array|null
    {
        if (\is_null($requestException)) {
            return null;
        }

        \assert(!\is_null($request), 'request must be not null if request exception is not null');

        return self::convertException($requestException);
    }

    /**
     * Conversion uncaught exception to array
     *
     * @param Throwable|null $uncaughtException
     * @return array|null
     */
    private static function convertUncaughtException(?Throwable $uncaughtException): array|null
    {
        if (\is_null($uncaughtException)) {
            return null;
        }

        return self::convertException($uncaughtException);
    }

    /**
     * Conversion exception to array
     *
     * @param Throwable $exception
     * @return array
     */
    private static function convertException(Throwable $exception): array
    {
        return [
            'class' => \get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'previousMessage' => $exception->getPrevious() ? $exception->getPrevious()->getMessage() : null,
        ];
    }
}
