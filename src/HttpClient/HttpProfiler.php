<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Http\Client\Request;
use Amp\Http\Client\Response;

class HttpProfiler
{
    /**
     * @param HttpResult[] $results
     */
    public function __construct(
        private array $results = [],
    ) {
    }

    /**
     * Retrieves results
     *
     * @return HttpResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Profiles successed response
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function profile(Request $request, Response $response): void
    {
        $this->results[] = new HttpResult($request, $response);
    }

    /**
     * Profiles response exception during sending request
     *
     * @param Request $request
     * @param \Throwable $exception
     * @return void
     */
    public function profileException(Request $request, \Throwable $exception): void
    {
        $this->results[] = new HttpResult($request, null, $exception);
    }

    /**
     * Profiles uncaught exception doing scenario
     *
     * @param \Throwable $exception
     * @return void
     */
    public function profileUncaughtException(\Throwable $exception): void
    {
        $this->results[] = new HttpResult(null, null, null, $exception);
    }
}
