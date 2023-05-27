<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

/**
 * Executable scenario instruction
 */
interface InstructionInterface
{
    /**
     * Executes this instruction
     *
     * @return void
     */
    public function execute(): void;
}
