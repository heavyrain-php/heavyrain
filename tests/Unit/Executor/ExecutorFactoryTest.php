<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Client\BuzzClientInterface;
use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionFunction;

#[CoversClass(ExecutorFactory::class)]
#[CoversClass(ExecutorConfig::class)]
#[CoversClass(DefaultScenarioConfig::class)]
#[CoversClass(HttpProfiler::class)]
#[CoversClass(DefaultHttpBuilder::class)]
#[CoversClass(PsrInstructor::class)]
#[CoversClass(SyncExecutor::class)]
final class ExecutorFactoryTest extends TestCase
{
    public function testCreateSync(): void
    {
        $config = new ExecutorConfig('', new DefaultScenarioConfig(), '');
        /** @var \PHPUnit\Framework\MockObject\MockObject&ReflectionFunction */
        $scenarioFunction = $this->createMock(ReflectionFunction::class);
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&BuzzClientInterface */
        $buzzClient = $this->createMock(BuzzClientInterface::class);

        $factory = new ExecutorFactory($config, $scenarioFunction, $profiler);

        $actual = $factory->createSync($buzzClient);

        self::assertInstanceOf(SyncExecutor::class, $actual);
    }
}
