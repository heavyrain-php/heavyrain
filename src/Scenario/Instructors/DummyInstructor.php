<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructors;

use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\InstructionInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Heavyrain\Scenario\Response;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Makes dummy instructor for testing
 */
class DummyInstructor implements InstructorInterface
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

    public function get(string $path): Response
    {
        return $this->request($this->requestFactory->createRequest('GET', $path));
    }

    public function request(RequestInterface $request): Response
    {
        $this->instructions[] = new HttpRequestInstruction($request);
        return new Response($this->responseFactory->createResponse());
    }

    public function waitSec(int|float $sec): void
    {
        $this->instructions[] = new WaitInstruction($sec);
    }
}
