<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Scenario Executor
 */
interface ExecutorInterface
{
    /**
     * Makes GET request
     *
     * @param string $path HTTP Path
     * @return ResponseInterface Result
     */
    public function get(string $path): ResponseInterface;

    /**
     * Makes HTTP request
     *
     * @param RequestInterface $request Request instance
     * @return ResponseInterface Result
     */
    public function request(RequestInterface $request): ResponseInterface;

    /**
     * Asserts HTTP response
     *
     * @param ResponseInterface $response
     * @param Closure $assertionFunc
     * @return void
     */
    public function assertResponse(ResponseInterface $response, Closure $assertionFunc): void;

    /**
     * Waits target second
     *
     * @param integer|float $sec wait seconds
     * @return void
     */
    public function waitSec(int|float $sec): void;
}
