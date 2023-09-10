<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

/**
 * Reporter interface
 */
interface ReporterInterface
{
    /**
     * Reports HTTP profiling results
     *
     * @param array $results
     * @return void
     */
    public function report(array $results): void;
}
