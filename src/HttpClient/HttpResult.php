<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use ArrayIterator;
use Heavyrain\Contracts\HttpResultInterface;
use Throwable;
use Traversable;

/**
 * HTTP profiling result
 * for reduce memory and reference leaks, all data is stored in array
 */
final class HttpResult implements HttpResultInterface
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
        return \array_key_exists($offset, $this->result);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!\array_key_exists($offset, $this->result)) {
            throw new \RuntimeException('invalid offset. supported: request, response, requestException, uncaughtException');
        }
        return $this->result[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        throw new \LogicException('HttpResult is immutable');
    }

    public function offsetUnset(mixed $offset): void {
        throw new \LogicException('HttpResult is immutable');
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->result);
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
                'start' => $response->getRequest()->hasAttribute(self::START_KEY) ? $response->getRequest()->getAttribute(self::START_KEY) : null,
                'connect' => $response->getRequest()->hasAttribute(self::CONNECT_KEY) ? $response->getRequest()->getAttribute(self::CONNECT_KEY) : null,
                'sendStart' => $response->getRequest()->hasAttribute(self::SEND_START_KEY) ? $response->getRequest()->getAttribute(self::SEND_START_KEY) : null,
                'sendEnd' => $response->getRequest()->hasAttribute(self::SEND_END_KEY) ? $response->getRequest()->getAttribute(self::SEND_END_KEY) : null,
                'receiveStart' => $response->getRequest()->hasAttribute(self::RECEIVE_START_KEY) ? $response->getRequest()->getAttribute(self::RECEIVE_START_KEY) : null,
                'receiveEnd' => $response->getRequest()->hasAttribute(self::RECEIVE_END_KEY) ? $response->getRequest()->getAttribute(self::RECEIVE_END_KEY) : null,
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
            'previousMessage' => $exception->getPrevious()?->getMessage(),
        ];
    }
}
