<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\ExecutorInterface;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use ReflectionFunction;

/**
 * Simply executes synchronized
 */
class SyncExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly ReflectionFunction $scenarioFunction,
        private readonly HttpProfiler $profiler,
        private readonly InstructorInterface $inst,
    ) {
    }

    public function execute(): void
    {
        try {
            $this->scenarioFunction->invoke($this->inst);
            /** @psalm-suppress ArgumentTypeCoercion */
            \usleep(\intval(\round($this->config->waitAfterScenarioSec * 1_000_000)));
        } catch (\Throwable $e) {
            $this->profiler->profileException($e);
        }
    }
}
