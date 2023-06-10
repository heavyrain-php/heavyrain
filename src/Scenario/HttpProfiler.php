<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\HttpProfilerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * HTTPResult profiler
 * TODO: Aggregation by unixtime
 * TODO: Aggregation by path(or Path-Tag request header)
 * TODO: Aggregation by moving average method
 */
final class HttpProfiler implements HttpProfilerInterface
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

    public function getResults(): array
    {
        return [...$this->results];
    }

    public function getExceptions(): array
    {
        return [...$this->exceptions];
    }

    public function profileException(Throwable $exception): void
    {
        $this->exceptions[] = $exception;
    }

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
