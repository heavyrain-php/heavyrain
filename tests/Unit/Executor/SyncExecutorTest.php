<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\CancellationToken;
use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExecutorConfig::class)]
#[CoversClass(SyncExecutor::class)]
#[CoversClass(CancellationToken::class)]
#[CoversClass(DefaultScenarioConfig::class)]
#[CoversClass(HttpProfiler::class)]
final class SyncExecutorTest extends TestCase
{
    public function testExecuteFailed(): void
    {
        $config = new ExecutorConfig('', new DefaultScenarioConfig(), '', 0.0001, 0.0002);
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&InstructorInterface */
        $inst = $this->createMock(InstructorInterface::class);
        $token = new CancellationToken();

        $exception = new \RuntimeException('ERROR');
        $scenarioFunction = static function () use ($token, $exception): void {
            $token->cancel();
            throw $exception;
        };

        $executor = new SyncExecutor($config, $scenarioFunction, $profiler, $inst);

        $executor->execute($token);

        self::assertCount(1, $profiler->getExceptions());
        self::assertSame($exception, $profiler->getExceptions()[0]);
    }
}
