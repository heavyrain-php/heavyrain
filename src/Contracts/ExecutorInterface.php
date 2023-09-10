<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

/**
 * Scenario executor
 */
interface ExecutorInterface
{
    /**
     * Executes scenario
     * It must not throw Exception
     *
     * @param CancellationTokenInterface $token
     * @return iterable
     */
    public function execute(CancellationTokenInterface $token): iterable;
}
