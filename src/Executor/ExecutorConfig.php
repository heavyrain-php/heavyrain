<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

final class ExecutorConfig
{
    /**
     * @param string $baseUri
     * @param bool $sslVerify
     * @param int|float|null $timeout
     * @param array<string, string> $defaultHeaders
     */
    public function __construct(
        public readonly string $baseUri,
        public readonly bool $sslVerify = false,
        public readonly int|float|null $timeout = null,
        public readonly array $defaultHeaders = [],
    ) {
    }
}
