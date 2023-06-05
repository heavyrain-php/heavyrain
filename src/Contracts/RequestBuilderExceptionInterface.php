<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Throwable;

/**
 * Emits when building request was failed
 */
interface RequestBuilderExceptionInterface extends Throwable
{
}
