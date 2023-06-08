<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\CancellationTokenInterface;

final class CancellationToken implements CancellationTokenInterface
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
