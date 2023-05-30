<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Tests\Integration;

use Buzz\Client\BuzzClientInterface;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\HttpResult;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Scenario\Response;
use Heavyrain\Support\DefaultHttpBuilder;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(HttpProfiler::class)]
#[CoversClass(PsrInstructor::class)]
#[CoversClass(DefaultHttpBuilder::class)]
#[CoversClass(HttpResult::class)]
#[CoversClass(HttpRequestInstruction::class)]
#[CoversClass(WaitInstruction::class)]
#[CoversClass(Response::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&StreamInterface $streamMock */
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('__toString')
            ->willReturn('');
        /** @var \PHPUnit\Framework\MockObject\MockObject&ResponseInterface $responseMock */
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        /** @var \PHPUnit\Framework\MockObject\MockObject&BuzzClientInterface $clientMock */
        $clientMock = $this->createMock(BuzzClientInterface::class);
        $clientMock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseMock);
        $builder = new DefaultHttpBuilder();
        $profiler = new HttpProfiler();
        $inst = new PsrInstructor(
            $builder->getUriFactory(),
            $builder->getRequestFactory()->createRequest('GET', ''),
            $builder->buildClient($clientMock),
            $profiler,
        );

        $func = static function (InstructorInterface $inst): void {
            $inst->get('/');
            $inst->waitSec(0.0001);
        };

        $func($inst);
    }
}
