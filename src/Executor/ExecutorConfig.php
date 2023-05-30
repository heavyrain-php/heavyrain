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
     * @param int|float $waitAfterSendRequestSec
     * @param bool $sslVerify
     * @param int|float $timeout
     */
    public function __construct(
        public readonly string $baseUri,
        public readonly ScenarioConfigInterface $scenarioConfig,
        public readonly string $userAgentBase,
        public readonly int|float $waitAfterScenarioSec = 1.0,
        public readonly int|float $waitAfterSendRequestSec = 1.0,
        public readonly bool $sslVerify = false,
        public readonly int|float $timeout = 0,
    ) {
        if (\floatval($this->waitAfterScenarioSec) < 0.0) {
            throw new LogicException('waitAfterScenarioSec must be positive-numeric');
        } elseif (\floatval($this->waitAfterSendRequestSec) < 0.0) {
            throw new LogicException('waitAfterSendRequestSec must be positive-numeric');
        } elseif (\floatval($this->timeout) < 0.0) {
            throw new LogicException('timeout must be positive-numeric');
        }
    }
}
