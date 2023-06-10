<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use JsonSerializable;
use Stringable;

/**
 * HTTP Result information
 */
interface HttpResultInterface extends JsonSerializable, Stringable
{
    /**
     * Returns request information
     *
     * @return array
     * @psalm-return array{
     *   protocolVersion: string,
     *   method: string,
     *   uri: string,
     *   path: string,
     *   headers: array<string[]>
     * }
     * @phpstan-return array{
     *   protocolVersion: string,
     *   method: string,
     *   uri: string,
     *   path: string,
     *   headers: array<string[]>
     * }
     * @phan-return array{
     *   protocolVersion: string,
     *   method: string,
     *   uri: string,
     *   path: string,
     *   headers: array<string[]>
     * }
     */
    public function getRequest(): array;

    /**
     * Returns response information
     *
     * @return ?array
     * @psalm-return null|array{
     *   statusCode: int,
     *   reasonPhrase: string,
     *   bodyLength: int,
     *   headers: array<string[]>
     * }
     * @phpstan-return null|array{
     *   statusCode: int,
     *   reasonPhrase: string,
     *   bodyLength: int,
     *   headers: array<string[]>
     * }
     * @phan-return null|array{
     *   statusCode: int,
     *   reasonPhrase: string,
     *   bodyLength: int,
     *   headers: array<string[]>
     * }
     */
    public function getResponse(): ?array;

    /**
     * Returns exception information
     *
     * @return ?array
     * @psalm-return null|array{
     *   name: string,
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     * @phpstan-return null|array{
     *   name: string,
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     * @phan-return null|array{
     *   name: string,
     *   message: string,
     *   code: int|string,
     *   previousMessage: ?string
     * }
     */
    public function getException(): ?array;

    /**
     * Returns curl result
     *
     * @return ?array
     * @psalm-return null|array<array-key, mixed>
     * @phpstan-return null|array<array-key, mixed>
     * @phan-return null|array<array-key, mixed>
     */
    public function getCurlInfo(): ?array;
}
