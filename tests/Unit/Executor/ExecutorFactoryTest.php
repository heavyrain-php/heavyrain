<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Client\BuzzClientInterface;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\RequestBuilder;
use Heavyrain\Support\DefaultHttpBuilder;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ExecutorFactory::class)]
#[UsesClass(ExecutorConfig::class)]
#[UsesClass(DefaultScenarioConfig::class)]
#[UsesClass(HttpProfiler::class)]
#[UsesClass(DefaultHttpBuilder::class)]
#[UsesClass(RequestBuilder::class)]
#[UsesClass(WaitSendRequestMiddleware::class)]
#[UsesClass(Client::class)]
#[UsesClass(SyncExecutor::class)]
final class ExecutorFactoryTest extends TestCase
{
    public function testCreateSync(): void
    {
        $config = new ExecutorConfig('http://localhost', new DefaultScenarioConfig(), '');
        $profiler = new HttpProfiler();
        /** @var \PHPUnit\Framework\MockObject\MockObject&BuzzClientInterface */
        $buzzClient = $this->createMock(BuzzClientInterface::class);

        $factory = new ExecutorFactory($config, static fn () => 1, $profiler);

        $actual = $factory->createSync($buzzClient);

        self::assertInstanceOf(SyncExecutor::class, $actual);
    }
}
