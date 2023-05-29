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
     * Makes GET JSON HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function getJson(
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
     * @param array|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function postJson(
        string $path,
        array|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes HEAD HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function head(
        string $path,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes HEAD JSON HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function headJson(
        string $path,
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
     * Makes PUT JSON HTTP request
     *
     * @param string $path
     * @param array|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function putJson(
        string $path,
        array|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes DELETE HTTP request
     *
     * @param string $path
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function delete(
        string $path,
        string|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes DELETE JSON HTTP request
     *
     * @param string $path
     * @param array|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function deleteJson(
        string $path,
        array|null $body = null,
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
     * Makes PATCH JSON HTTP request
     *
     * @param string $path
     * @param array|null $body
     * @param non-empty-string $version Protocol version
     * @param array<non-empty-string, string|string[]> $headers
     * @return Response
     */
    public function patchJson(
        string $path,
        array|null $body = null,
        string $version = '1.1',
        array $headers = [],
    ): Response;

    /**
     * Makes HTTP request
     *
     * @param RequestInterface $request
     * @return Response
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
