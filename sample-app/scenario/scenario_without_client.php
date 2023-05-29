<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\InstructorInterface;

return static function (InstructorInterface $inst): void {
    $inst->get('/')
        ->assertOk()
        ->assertHeaderHas('Content-Length', '27')
        ->assertContentHas('Hello world.');

    $inst->postJson('/json', ['a' => 'is b'])
        ->assertOk()
        ->assertJsonHas('hello', 'world.');

    $inst->get('/undefinedpath')
        ->assertStatusCode(404);

    $inst->getJson('/users/1')
        ->assertOk();

    $inst->getJson('/posts/?postId=2')
        ->assertOk();
};
