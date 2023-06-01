<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use Throwable;

/**
 * Emits when json_encode has failed
 */
interface BodyEncodeExceptionInterface extends Throwable
{
}
