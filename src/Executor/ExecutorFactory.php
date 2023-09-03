<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Closure;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Contracts\ExecutorInterface;
use Heavyrain\Scenario\HttpProfiler;

final class ExecutorFactory
{
    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly Closure $scenarioFunction,
        private readonly HttpProfiler $profiler,
        private readonly ClientInterface $client,
    ) {
    }

    public function createSync(): ExecutorInterface
    {
        return new SyncExecutor(
            $this->config,
            $this->scenarioFunction,
            $this->profiler,
            $this->client,
        );
    }
}
