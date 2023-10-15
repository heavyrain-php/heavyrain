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

use function Amp\async;

/**
 * Executes asynchronized with amphp
 */
class AmphpExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly Closure $scenarioFunction,
        private readonly ClientFactory $factory,
        private readonly HttpProfiler $profiler,
        private readonly int $userCount,
    ) {
    }

    public function execute(CancellationTokenInterface $token): iterable
    {
        $scenarioFunc = $this->scenarioFunction;
        $factory = $this->factory;
        $profiler = $this->profiler;

        for ($i = 0; $i < $this->userCount; $i++) {
            // TODO: ramp-up users
            yield async(static function () use ($token, $scenarioFunc, $factory, $profiler): void {
                while (true) {
                    try {
                        $scenarioFunc($factory->create($token));
                    } catch (RequestException $e) {
                        // do nothing because Heavyrain\HttpClient\RequestException was handled in AmphpClient
                    } catch (Throwable $e) {
                        $profiler->profileUncaughtException($e);
                    }
                    \sleep(1); // sleep 1 second to prevent CPU overload
                }
            });
        }
    }
}
