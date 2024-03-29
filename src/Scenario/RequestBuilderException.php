<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\RequestBuilderExceptionInterface;
use LogicException;

final class RequestBuilderException extends LogicException implements RequestBuilderExceptionInterface
{
}
