<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Scenario Executor
 */
interface ExecutorInterface
{
    /**
     * Make GET request
     *
     * @param string $path HTTP Path
     * @return ResponseInterface Result
     */
    public function get(string $path): ResponseInterface;

    /**
     * Make HTTP request
     *
     * @param RequestInterface $request Request instance
     * @return ResponseInterface Result
     */
    public function request(RequestInterface $request): ResponseInterface;

    /**
     * Wait target second
     *
     * @param integer|float $sec wait seconds
     * @return void
     */
    public function waitSec(int|float $sec): void;
}
