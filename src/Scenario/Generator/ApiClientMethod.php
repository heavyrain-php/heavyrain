<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

/**
 * Api client method definition
 */
final class ApiClientMethod
{
    public function __construct(
        public readonly string $methodName,
    ) {
    }
}
