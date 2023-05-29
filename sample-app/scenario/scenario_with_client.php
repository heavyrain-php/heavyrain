<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\InstructorInterface;

require_once __DIR__ . '/ScenarioClient.php';

return static function (InstructorInterface $inst): void {
    $client = new ScenarioClient($inst);
    $client->getIndex()
        ->assertOk()
        ->assertHeaderHas('Content-Length', '27')
        ->assertContentHas('Hello world.');

    $client->postJson(['a' => 'is b'])
        ->assertOk()
        ->assertJsonHas('hello', 'world.');

    $client->getUsers(1)
        ->assertOk();

    $client->getPosts(2)
        ->assertOk();
};
