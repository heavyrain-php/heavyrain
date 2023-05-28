<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\InstructorInterface;
use ReflectionFunction;

class Executor
{
    public function __construct(
        private readonly ReflectionFunction $scenarioFunction,
        private readonly InstructorInterface $inst,
    ) {
    }

    public function execute(): void
    {
        try {
            $this->scenarioFunction->invoke($this->inst);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
