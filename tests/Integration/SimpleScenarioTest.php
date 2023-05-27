<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Integration;

use Heavyrain\Scenario\Executors\DummyExecutor;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DummyExecutor::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        $func = require __DIR__ . '/../Stubs/simple_scenario.php';
        $executor = new DummyExecutor();

        $func($executor);

        $instructions = $executor->getInstructions();

        $getRootHttpInstruction = $instructions[0];
        $this->assertInstanceOf(HttpRequestInstruction::class, $getRootHttpInstruction);
        assert($getRootHttpInstruction instanceof HttpRequestInstruction);
        $this->assertSame('GET', $getRootHttpInstruction->request->getMethod());
        $this->assertSame('/', $getRootHttpInstruction->request->getUri()->getPath());

        $waitOneSecInstruction = $instructions[1];
        $this->assertInstanceOf(WaitInstruction::class, $waitOneSecInstruction);
        assert($waitOneSecInstruction instanceof WaitInstruction);
        $this->assertSame(1.0, $waitOneSecInstruction->sec);
    }
}
