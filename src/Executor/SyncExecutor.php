<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Closure;
use Heavyrain\Contracts\CancellationTokenInterface;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Contracts\ExecutorInterface;
use Heavyrain\Scenario\HttpProfiler;
use Throwable;

/**
 * Simply executes synchronized
 */
class SyncExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly Closure $scenarioFunction,
        private readonly HttpProfiler $profiler,
        private readonly ClientInterface $cl,
    ) {
    }

    public function execute(CancellationTokenInterface $token): void
    {
        while (true) {
            if ($token->isCancelled()) {
                return;
            }

            try {
                ($this->scenarioFunction)($this->cl);
            } catch (Throwable $e) {
                $this->profiler->profileException($e);
            }

            /** @var int<0, max> */
            $usec = \intval(\round(\abs($this->config->waitAfterScenarioSec) * 1_000_000));
            \usleep($usec);

            return;
        }
    }
}
