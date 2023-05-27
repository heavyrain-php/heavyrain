<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Instructors\GuzzleInstructorFactory;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(HttpRequestInstruction::class)]
#[CoversClass(WaitInstruction::class)]
#[CoversClass(GuzzleInstructorFactory::class)]
#[CoversClass(PsrInstructor::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        $mock = new MockHandler([
            new Response(),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(compact('handler'));
        $inst = GuzzleInstructorFactory::create($client);

        $func = require __DIR__ . '/../Stubs/simple_scenario.php';
        $func($inst);

        $instructions = $inst->getInstructions();

        $this->assertCount(2, $instructions);

        $getRootHttpInstruction = $instructions[0];
        $this->assertInstanceOf(HttpRequestInstruction::class, $getRootHttpInstruction);
        assert($getRootHttpInstruction instanceof HttpRequestInstruction);
        $this->assertSame('GET', $getRootHttpInstruction->request->getMethod());
        $this->assertSame('/', $getRootHttpInstruction->request->getUri()->getPath());

        $waitOneSecInstruction = $instructions[1];
        $this->assertInstanceOf(WaitInstruction::class, $waitOneSecInstruction);
        assert($waitOneSecInstruction instanceof WaitInstruction);
        $this->assertSame(1, $waitOneSecInstruction->sec);
    }
}
