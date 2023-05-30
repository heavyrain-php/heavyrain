<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionFunction;

#[CoversClass(SyncExecutor::class)]
#[CoversClass(ExecutorConfig::class)]
#[CoversClass(DefaultScenarioConfig::class)]
#[CoversClass(HttpProfiler::class)]
final class SyncExecutorTest extends TestCase
{
    public function testExecute(): void
    {
        $config = new ExecutorConfig('', new DefaultScenarioConfig(), '', 0.0001);
        /** @var \PHPUnit\Framework\MockObject\MockObject&ReflectionFunction */
        $scenarioFunction = $this->createMock(ReflectionFunction::class);
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&InstructorInterface */
        $inst = $this->createMock(InstructorInterface::class);

        $scenarioFunction->expects($this->once())
            ->method('invoke')
            ->with($inst);

        $executor = new SyncExecutor($config, $scenarioFunction, $profiler, $inst);

        $executor->execute();
    }

    public function testExecuteFailed(): void
    {
        $config = new ExecutorConfig('', new DefaultScenarioConfig(), '', 0.0001);
        /** @var \PHPUnit\Framework\MockObject\MockObject&ReflectionFunction */
        $scenarioFunction = $this->createMock(ReflectionFunction::class);
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&InstructorInterface */
        $inst = $this->createMock(InstructorInterface::class);

        $exception = new \RuntimeException('ERROR');
        $scenarioFunction->expects($this->once())
            ->method('invoke')
            ->with($inst)
            ->willThrowException($exception);

        $executor = new SyncExecutor($config, $scenarioFunction, $profiler, $inst);

        $executor->execute();

        self::assertCount(1, $profiler->getExceptions());
        self::assertSame($exception, $profiler->getExceptions()[0]);
    }
}
