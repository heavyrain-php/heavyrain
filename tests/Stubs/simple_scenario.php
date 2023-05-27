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
    $response = $executor->get('/');
    $executor->assertResponse($response, static function () {
        // Do nothing
    });

    $executor->waitSec(1.0);
};
