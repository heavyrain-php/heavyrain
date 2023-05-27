<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructions;

use Heavyrain\Scenario\InstructionInterface;

/**
 * Makes sleep some seconds
 */
class WaitInstruction implements InstructionInterface
{
    public function __construct(
        public readonly int|float $sec,
    ) {
    }

    public function execute(): void
    {
        // TODO
    }
}
