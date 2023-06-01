<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\BodyEncodeExceptionInterface;
use RuntimeException;

final class BodyEncodeException extends RuntimeException implements BodyEncodeExceptionInterface
{
    public function __construct(
        string $jsonLastErrorMessage,
        int $jsonLastError,
        private readonly mixed $parsingBody,
    )
    {
        parent::__construct($jsonLastErrorMessage, $jsonLastError);
    }

    public function getParsingBody(): mixed
    {
        return $this->parsingBody;
    }
}
