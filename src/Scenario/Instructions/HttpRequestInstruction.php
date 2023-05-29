<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructions;

use Heavyrain\Scenario\InstructionInterface;
use Heavyrain\Scenario\RequestException;
use LogicException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Makes HTTP request
 */
class HttpRequestInstruction implements InstructionInterface
{
    private ?ResponseInterface $response = null;

    public function __construct(
        private readonly ClientInterface $client,
        public readonly RequestInterface $request,
    ) {
    }

    public function getResponse(): ResponseInterface
    {
        if (\is_null($this->response)) {
            throw new LogicException('getResponse before execution');
        }
        return $this->response;
    }

    public function execute(): void
    {
        try {
            $this->response = $this->client->sendRequest($this->request);
        } catch (Throwable $e) {
            throw new RequestException($this->request, $this->response, $e);
        }
    }
}
