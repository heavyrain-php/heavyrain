<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

/**
 * Scenario Instructor
 */
interface InstructorInterface
{
    /**
     * Makes GET HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function get(
        string $path,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes POST HTTP request
     *
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function post(
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes POST JSON HTTP request
     *
     * @param string $path
     * @param array $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function postJson(
        string $path,
        array $body,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes HEAD HTTP request
     *
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function head(
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes PUT HTTP request
     *
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function put(
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes DELETE request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function delete(
        string $path,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes OPTIONS HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function options(
        string $path,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes PATCH HTTP request
     *
     * @param string $path
     * @param string|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function patch(
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Waits target second
     *
     * @param integer|float $sec wait seconds
     * @return void
     */
    public function waitSec(int|float $sec): void;
}
