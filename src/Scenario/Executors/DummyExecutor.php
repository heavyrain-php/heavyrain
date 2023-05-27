<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Executors;

use Closure;
use Heavyrain\Scenario\ExecutorInterface;
use Heavyrain\Scenario\InstructionInterface;
use Heavyrain\Scenario\Instructions\AssertHttpResponseInstruction;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Makes dummy execution for testing
 */
class DummyExecutor implements ExecutorInterface
{
    /** @var InstructionInterface[] $instructions */
    private array $instructions = [];

    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * Retrieves instructions to execute
     *
     * @return InstructionInterface[]
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    public function get(string $path): ResponseInterface
    {
        return $this->request($this->requestFactory->createRequest('GET', $path));
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        $this->instructions[] = new HttpRequestInstruction($request);
        return $this->responseFactory->createResponse();
    }

    public function assertResponse(ResponseInterface $response, Closure $assertionFunc): void
    {
        $this->instructions[] = new AssertHttpResponseInstruction($response, $assertionFunc);
    }

    public function waitSec(int|float $sec): void
    {
        $this->instructions[] = new WaitInstruction($sec);
    }
}
