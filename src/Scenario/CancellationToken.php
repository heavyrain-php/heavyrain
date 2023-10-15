<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Amp\Cancellation;
use Amp\DeferredCancellation;
use Heavyrain\Contracts\CancellationTokenInterface;

final class CancellationToken implements CancellationTokenInterface
{
    public function __construct(
        private readonly DeferredCancellation $cancellation,
    ) {
    }

    public function getCancellation(): Cancellation
    {
        return $this->cancellation->getCancellation();
    }

    public function isCancelled(): bool
    {
        return $this->cancellation->isCancelled();
    }

    public function cancel(): void
    {
        $this->cancellation->cancel();
    }
}
