<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Scenario\CancellationToken;
use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(SyncExecutor::class)]
#[UsesClass(ExecutorConfig::class)]
#[UsesClass(CancellationToken::class)]
#[UsesClass(DefaultScenarioConfig::class)]
#[UsesClass(HttpProfiler::class)]
final class SyncExecutorTest extends TestCase
{
    public function testExecuteFailed(): void
    {
        $config = new ExecutorConfig('', new DefaultScenarioConfig(), '', 0.0001, 0.0002);
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface */
        $cl = $this->createMock(ClientInterface::class);
        $token = new CancellationToken();

        $exception = new \RuntimeException('ERROR');
        $scenarioFunction = static function (ClientInterface $cl) use ($token, $exception): void {
            self::assertInstanceOf(ClientInterface::class, $cl);
            $token->cancel();
            throw $exception;
        };

        $executor = new SyncExecutor($config, $scenarioFunction, $profiler, $cl);

        $executor->execute($token);

        self::assertCount(1, $profiler->getExceptions());
        self::assertSame($exception, $profiler->getExceptions()[0]);
    }
}
