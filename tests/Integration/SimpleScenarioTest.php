<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Tests\Integration;

use Buzz\Client\BuzzClientInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(HttpRequestInstruction::class)]
#[CoversClass(WaitInstruction::class)]
#[CoversClass(PsrInstructor::class)]
#[CoversClass(DefaultHttpBuilder::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface $responseMock */
        $responseMock = $this->createMock(ResponseInterface::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&BuzzClientInterface $clientMock */
        $clientMock = $this->createMock(BuzzClientInterface::class);
        $clientMock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseMock);
        $builder = new DefaultHttpBuilder();
        $inst = new PsrInstructor(
            $builder->getRequestFactory()->createRequest('GET', '/'),
            $builder->buildClient($clientMock),
        );

        $func = static function (InstructorInterface $inst): void {
            $inst->get('/');
            $inst->waitSec(0.0001);
        };

        $func($inst);

        $instructions = $inst->getInstructions();

        $this->assertCount(2, $instructions);

        $getRootHttpInstruction = $instructions[0];
        $this->assertInstanceOf(HttpRequestInstruction::class, $getRootHttpInstruction);
        \assert($getRootHttpInstruction instanceof HttpRequestInstruction);
        $this->assertSame('GET', $getRootHttpInstruction->request->getMethod());
        $this->assertSame('/', $getRootHttpInstruction->request->getUri()->getPath());

        $waitOneSecInstruction = $instructions[1];
        $this->assertInstanceOf(WaitInstruction::class, $waitOneSecInstruction);
        \assert($waitOneSecInstruction instanceof WaitInstruction);
        $this->assertSame(0.0001, $waitOneSecInstruction->sec);
    }
}
