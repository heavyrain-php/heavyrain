<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\ScenarioConfigInterface;
use LogicException;

final class ExecutorConfig
{
    /**
     * @param string $baseUri
     * @param ScenarioConfigInterface $scenarioConfig
     * @param string $userAgentBase
     * @param int|float $waitAfterScenarioSec
     * @param bool $sslVerify
     * @param int|float|null $timeout
     */
    public function __construct(
        public readonly string $baseUri,
        public readonly ScenarioConfigInterface $scenarioConfig,
        public readonly string $userAgentBase,
        public readonly int|float $waitAfterScenarioSec = 1.0,
        public readonly bool $sslVerify = false,
        public readonly int|float|null $timeout = null,
    ) {
        if (\floatval($this->waitAfterScenarioSec) < 0.0) {
            throw new LogicException('waitAfterScenarioSec must be positive-numeric');
        } elseif (!\is_null($timeout) && \floatval($this->timeout) < 0.0) {
            throw new LogicException('timeout must be positive-numeric');
        }
    }
}
