<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Integration;

use Buzz\Client\BuzzClientInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Instructors\BuzzInstructorFactory;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use Heavyrain\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(HttpRequestInstruction::class)]
#[CoversClass(WaitInstruction::class)]
#[CoversClass(BuzzInstructorFactory::class)]
#[CoversClass(PsrInstructor::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface $responseMock */
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);
        /** @var \PHPUnit\Framework\MockObject\MockObject&BuzzClientInterface $clientMock */
        $clientMock = $this->createMock(BuzzClientInterface::class);
        $clientMock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseMock);
        $builder = new DefaultHttpBuilder();
        $inst = new PsrInstructor(
            $builder->getRequestFactory(),
            $builder->getStreamFactory(),
            $builder->buildClient($clientMock),
            '',
        );

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
