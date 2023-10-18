<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Contracts;

use ArrayAccess;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

/**
 * HTTP profiling result
 * for reduce memory and reference leaks, all data is stored in array
 * @template-extends ArrayAccess<'request'|'response'|'requestException'|'uncaughtException', ?array<string, array<string, mixed>>>
 * @template-extends IteratorAggregate<'request'|'response'|'requestException'|'uncaughtException', ?array<string, array<string, mixed>>>
 */
interface HttpResultInterface extends ArrayAccess, JsonSerializable, Stringable, IteratorAggregate
{
}
