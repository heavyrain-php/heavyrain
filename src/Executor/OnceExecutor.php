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
use Heavyrain\HttpClient\HttpProfiler;
use Heavyrain\HttpClient\RequestException;
use Throwable;

/**
 * Simply executes synchronized once
 */
class OnceExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly Closure $scenarioFunction,
        private readonly ClientFactory $factory,
        private readonly HttpProfiler $profiler,
    ) {
    }

    public function execute(CancellationTokenInterface $token): iterable
    {
        try {
            yield ($this->scenarioFunction)($this->factory->create($token));
        } catch (RequestException $e) {
            // do nothing because Heavyrain\HttpClient\RequestException was handled in AmphpClient
        } catch (Throwable $e) {
            $this->profiler->profileUncaughtException($e);
        }
    }
}
