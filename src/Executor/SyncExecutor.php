<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Closure;
use Heavyrain\Contracts\CancellationTokenInterface;
use Heavyrain\Contracts\ExecutorInterface;
use Heavyrain\HttpClient\ClientFactory;
use Heavyrain\HttpClient\RequestException;
use Throwable;

/**
 * Simply executes synchronized
 */
class SyncExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly Closure $scenarioFunction,
        private readonly ClientFactory $factory,
    ) {
    }

    public function execute(CancellationTokenInterface $token): iterable
    {
        while (true) {
            if ($token->isCancelled()) {
                return;
            }

            try {
                ($this->scenarioFunction)($this->factory->create());
            } catch (RequestException $e) {
                // do nothing because Heavyrain\HttpClient\RequestException was handled in AmphpClient
            } catch (Throwable $e) {
                $this->factory->profiler->profileUncaughtException($e);
            }

            yield null;
        }
    }
}
