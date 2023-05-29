<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\ScenarioConfigInterface;

final class ExecutorConfig
{
    /**
     * @param string $baseUri
     * @param ScenarioConfigInterface $scenarioConfig
     * @param string $userAgentBase
     * @param bool $sslVerify
     * @param int|float|null $timeout
     */
    public function __construct(
        public readonly string $baseUri,
        public readonly ScenarioConfigInterface $scenarioConfig,
        public readonly string $userAgentBase,
        public readonly bool $sslVerify = false,
        public readonly int|float|null $timeout = null,
    ) {
    }
}
