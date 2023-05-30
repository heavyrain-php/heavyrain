<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\InstructorInterface;

/**
 * Simple scenario function
 */
return static function (InstructorInterface $inst): void {
    $inst->get('/')->assertOk();
    $inst->waitSec(0.1);
};
