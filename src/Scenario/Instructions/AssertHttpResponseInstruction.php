<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructions;

use Closure;
use Heavyrain\Scenario\InstructionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Asserts HTTP response
 */
class AssertHttpResponseInstruction implements InstructionInterface
{
    public function __construct(
        public readonly ResponseInterface $response,
        public readonly Closure $assertionFunc,
    ) {
    }

    public function execute(): void
    {
        // TODO
    }
}
