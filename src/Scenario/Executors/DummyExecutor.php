<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Executors;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Heavyrain\Scenario\ExecutorInterface;
use Heavyrain\Scenario\InstructionInterface;
use Heavyrain\Scenario\Instructions\HttpRequestInstruction;
use Heavyrain\Scenario\Instructions\WaitInstruction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Makes dummy execution for testing
 */
class DummyExecutor implements ExecutorInterface
{
    /** @var InstructionInterface[] $instructions */
    private array $instructions = [];

    public function __construct()
    {
        if (!class_exists(Response::class)) {
            throw new \RuntimeException('You should require guzzlehttp/psr7 to use ' . __CLASS__);
        }
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
        return $this->request(new Request('GET', $path));
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        $this->instructions[] = new HttpRequestInstruction($request);
        return new Response();
    }

    public function waitSec(int|float $sec): void
    {
        $this->instructions[] = new WaitInstruction($sec);
    }
}
