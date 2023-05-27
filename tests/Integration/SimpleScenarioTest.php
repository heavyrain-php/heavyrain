<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Integration;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Heavyrain\Scenario\Executors\DummyExecutor;
use Heavyrain\Scenario\Instructions\AssertHttpResponseInstruction;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
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
        $func = require __DIR__ . '/../Stubs/simple_scenario.php';
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
        $executor = new DummyExecutor($requestFactory, $responseFactory);

        $func($executor);

        $instructions = $executor->getInstructions();

        $this->assertCount(3, $instructions);

        $getRootHttpInstruction = $instructions[0];
        $this->assertInstanceOf(HttpRequestInstruction::class, $getRootHttpInstruction);
        assert($getRootHttpInstruction instanceof HttpRequestInstruction);
        $this->assertSame('GET', $getRootHttpInstruction->request->getMethod());
        $this->assertSame('/', $getRootHttpInstruction->request->getUri()->getPath());

        $assertResponseInstruction = $instructions[1];
        $this->assertInstanceOf(AssertHttpResponseInstruction::class, $assertResponseInstruction);
        assert($assertResponseInstruction instanceof AssertHttpResponseInstruction);

        $waitOneSecInstruction = $instructions[2];
        $this->assertInstanceOf(WaitInstruction::class, $waitOneSecInstruction);
        assert($waitOneSecInstruction instanceof WaitInstruction);
        $this->assertSame(1.0, $waitOneSecInstruction->sec);
    }
}
