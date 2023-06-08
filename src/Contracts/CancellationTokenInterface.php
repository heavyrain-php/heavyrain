<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

interface CancellationTokenInterface
{
    public function isCancelled(): bool;

    public function cancel(): void;
}
