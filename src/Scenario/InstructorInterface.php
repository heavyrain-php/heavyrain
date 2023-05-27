<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\RequestInterface;

/**
 * Scenario Instructor
 */
interface InstructorInterface
{
    /**
     * Makes GET request
     *
     * @param string $path HTTP Path
     * @return Response Result
     */
    public function get(string $path): Response;

    /**
     * Makes HTTP request
     *
     * @param RequestInterface $request Request instance
     * @return Response Result
     */
    public function request(RequestInterface $request): Response;

    /**
     * Waits target second
     *
     * @param integer|float $sec wait seconds
     * @return void
     */
    public function waitSec(int|float $sec): void;
}
