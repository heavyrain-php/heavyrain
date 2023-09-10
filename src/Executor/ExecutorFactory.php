<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Closure;
use Heavyrain\Contracts\ExecutorInterface;
use Heavyrain\HttpClient\ClientFactory;
use Heavyrain\HttpClient\HttpProfiler;

final class ExecutorFactory
{
    public function __construct(
        private readonly Closure $scenarioFunction,
        private readonly HttpProfiler $profiler,
        private readonly string $baseUri,
    ) {
    }

    public function createSync(): ExecutorInterface
    {
        return new SyncExecutor(
            $this->scenarioFunction,
            new ClientFactory($this->profiler, $this->baseUri),
        );
    }
}
