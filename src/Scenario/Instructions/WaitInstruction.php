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
        /** @var int<0, max> $microsec */
        $microsec = \intval(\abs(\round($this->sec * 1000.0 * 1000.0, 0)));
        usleep($microsec);
    }
}
