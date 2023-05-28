<?php

/**
 * @license MIT
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();

$app->get('/', Heavyrain\Web\Controllers\IndexController::class);

$app->run();
