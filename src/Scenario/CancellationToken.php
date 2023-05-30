<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

final class CancellationToken
{
    private bool $isCancelled = false;

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function cancel(): void
    {
        $this->isCancelled = true;
    }
}
