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
     * @param HttpProfilerInterface $profiler
     * @return void
     */
    public function report(HttpProfilerInterface $profiler): void;
}
