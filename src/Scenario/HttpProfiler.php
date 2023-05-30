<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * HTTPResult profiler
 * TODO: Aggregation by unixtime
 * TODO: Aggregation by path(or Path-Tag request header)
 * TODO: Aggregation by moving average method
 */
final class HttpProfiler
{
    /** @var HttpResult[] */
    private array $results;

    /** @var Throwable[] */
    private array $exceptions;

    public function __construct()
    {
        $this->results = [];
        $this->exceptions = [];
    }

    /**
     * Get HttpResults
     *
     * @return HttpResult[]
     */
    public function getResults(): array
    {
        return [...$this->results];
    }

    /**
     * Get Exceptions
     *
     * @return Throwable[]
     */
    public function getExceptions(): array
    {
        return [...$this->exceptions];
    }

    /**
     * Profile Exception
     *
     * @param Throwable $exception
     * @return void
     */
    public function profileException(Throwable $exception): void
    {
        $this->exceptions[] = $exception;
    }

    /**
     * Profile HttpResult
     *
     * @param float $startMicrotime
     * @param float $endMicrotime
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return HttpResult
     */
    public function profile(
        float $startMicrotime,
        float $endMicrotime,
        RequestInterface $request,
        ResponseInterface $response,
    ): HttpResult {
        $result = new HttpResult(
            $startMicrotime,
            $endMicrotime,
            $request,
            $response,
        );
        $this->results[] = $result;
        return $result;
    }
}
