<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Profiles HTTP results
 */
interface HttpProfilerInterface
{
    /**
     * Returns results
     *
     * @return array
     * @psalm-return array<string, HttpResultInterface[]>
     * @phpstan-return array<string, HttpResultInterface[]>
     * @phan-return array<string, HttpResultInterface[]>
     */
    public function getResults(): array;

    /**
     * Returns uncaught exceptions
     *
     * @return Throwable[]
     */
    public function getExceptions(): array;

    /**
     * Profiles HTTP result
     *
     * @param float $startMicrotime
     * @param float $endMicrotime
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return HttpResultInterface
     */
    public function profile(
        float $startMicrotime,
        float $endMicrotime,
        RequestInterface $request,
        ResponseInterface $response,
    ): HttpResultInterface;

    /**
     * Profiles uncaught exception
     *
     * @param Throwable $exception
     * @return void
     */
    public function profileException(Throwable $exception): void;
}
