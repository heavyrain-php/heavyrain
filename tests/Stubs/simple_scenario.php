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
    $response = $inst->get('/');
    $response->assertOk();

    $inst->waitSec(1);
};
