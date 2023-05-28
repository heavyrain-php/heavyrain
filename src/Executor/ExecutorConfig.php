<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

final class ExecutorConfig
{
    public function __construct(
        public readonly string $baseUri,
        public readonly bool $sslVerify = false,
        public readonly int|float|null $timeout = null,
    ) {
    }
}
