<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Tests\Integration\Console\Commands;

use Heavyrain\Console\Commands\GenerateStubCommand;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(GenerateStubCommand::class)]
final class GenerateStubCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $this->expectException(\LogicException::class);

        $command = new GenerateStubCommand();
        $commandTester = new CommandTester($command);

        $commandTester->execute([], []);
    }
}
