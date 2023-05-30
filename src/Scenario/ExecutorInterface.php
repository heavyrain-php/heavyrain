<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

/**
 * Scenario executor
 */
interface ExecutorInterface
{
    /**
     * Executes scenario
     * It must not throw Exception
     *
     * @param CancellationToken $token
     * @return void
     */
    public function execute(CancellationToken $token): void;
}
