<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Tests\TestCase;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ExecutorConfig::class)]
#[CoversClass(DefaultScenarioConfig::class)]
final class ExecutorConfigTest extends TestCase
{
    #[Test]
    public function testConstructor(): void
    {
        $baseUri = 'http://localhost:8081';
        $scenarioConfig = new DefaultScenarioConfig();
        $userAgentBase = 'Custom User-Agent';
        $waitAfterScenarioSec = 1.0;
        $waitAfterSendRequestSec = 2.0;
        $sslVerify = false;
        $timeout = 1;
        $config = new ExecutorConfig(
            $baseUri,
            $scenarioConfig,
            $userAgentBase,
            $waitAfterScenarioSec,
            $waitAfterSendRequestSec,
            $sslVerify,
            $timeout,
        );

        self::assertSame($baseUri, $config->baseUri);
        self::assertSame($scenarioConfig, $config->scenarioConfig);
        self::assertSame($userAgentBase, $config->userAgentBase);
        self::assertSame($waitAfterScenarioSec, $config->waitAfterScenarioSec);
        self::assertSame($waitAfterSendRequestSec, $config->waitAfterSendRequestSec);
        self::assertSame($sslVerify, $config->sslVerify);
        self::assertSame($timeout, $config->timeout);
    }

    #[Test]
    public function testInvalidWaitAfterScenarioSec(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('waitAfterScenarioSec must be positive-numeric');

        new ExecutorConfig('', new DefaultScenarioConfig(), '', -1);
    }

    #[Test]
    public function testInvalidWaitAfterSendRequestSec(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('waitAfterSendRequestSec must be positive-numeric');

        new ExecutorConfig('', new DefaultScenarioConfig(), '', 1, -1);
    }

    #[Test]
    public function testInvalidTimeout(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('timeout must be positive-numeric');

        new ExecutorConfig('', new DefaultScenarioConfig(), '', 1, 1, false, -1);
    }
}
