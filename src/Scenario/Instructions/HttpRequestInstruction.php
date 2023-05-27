<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructions;

use Heavyrain\Scenario\InstructionInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Makes HTTP request
 */
class HttpRequestInstruction implements InstructionInterface
{
    public function __construct(
        public readonly RequestInterface $request,
    ) {
    }

    public function execute(): void
    {
        // TODO
    }
}
