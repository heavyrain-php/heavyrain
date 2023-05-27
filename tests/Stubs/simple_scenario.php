<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\ExecutorInterface;

/**
 * Simple scenario function
 */
return static function (ExecutorInterface $executor): void {
    $executor->get('/');
    $executor->waitSec(1.0);
};
