<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Generator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SplQueue;
use Throwable;

class HttpProfiler
{
    private const CURL_INFO_HEADER = '__curl_info';

    /** @var SplQueue<HttpResult> */
    private readonly SplQueue $queue;

    /** @var SplQueue<Throwable> */
    private readonly SplQueue $exceptionQueue;

    /**
     * @param SplQueue<HttpResult> $queue
     * @param SplQueue<Throwable> $exceptionQueue
     */
    public function __construct(
        ?SplQueue $queue = null,
        ?SplQueue $exceptionQueue = null,
    ) {
        /** @var SplQueue<HttpResult> */
        $this->queue = $queue ?? new SplQueue();
        /** @var SplQueue<Throwable> */
        $this->exceptionQueue = $exceptionQueue ?? new SplQueue();
    }

    /** @return Generator<int, HttpResult, HttpResult, void> */
    public function getResults(): Generator
    {
        foreach ($this->queue as $queue) {
            yield $queue;
        }
    }

    /** @return Generator<int, Throwable, Throwable, void> */
    public function getExceptions(): Generator
    {
        foreach ($this->exceptionQueue as $exception) {
            yield $exception;
        }
    }

    public function profileException(Throwable $exception): void
    {
        $this->exceptionQueue->enqueue($exception);
    }

    public function profile(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ): HttpResult {
        $result = new HttpResult(
            $request,
            $response,
            $exception,
            $this->getCurlInfo($response),
        );
        $this->queue->enqueue($result);
        return $result;
    }

    private function getCurlInfo(?ResponseInterface $response): ?array
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
}
