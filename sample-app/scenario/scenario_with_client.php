<?php

/**
 * @license MIT
 */

declare(strict_types=1);

return static function (ScenarioClient $client): void {
    $client->getIndex()
        ->assertOk()
        ->assertHeaderHas('Content-Length', '27')
        ->assertContentHas('Hello world.');

    $client->inst->waitSec(0.1);

    $client->postJson(['a' => 'is b'])
        ->assertOk()
        ->assertJsonHas('hello', 'world.');

    $client->getUsers(1)
        ->assertOk();

    $client->getPosts(2)
        ->assertOk();
};
