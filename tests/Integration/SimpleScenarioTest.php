<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Integration;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Heavyrain\Scenario\Instructions\AssertHttpResponseInstruction;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Instructors\DummyInstructor;
use Heavyrain\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(DummyExecutor::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        $requestFactory = new class () implements RequestFactoryInterface {
            public function createRequest(string $method, $uri): RequestInterface
            {
                return new Request($method, $uri);
            }
        };
        $responseFactory = new class () implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new Response($code, [], null, '1.1', $reasonPhrase);
            }
        };
        $inst = new DummyInstructor($requestFactory, $responseFactory);

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
