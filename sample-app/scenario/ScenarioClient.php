<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Response;

final class ScenarioClient
{
    public function __construct(private readonly InstructorInterface $inst)
    {
    }

    public function getIndex(): Response
    {
        return $this->inst->get('/');
    }

    public function postJson(array $body): Response
    {
        return $this->inst->postJson(
            path: '/json',
            body: $body,
            headers: ['Accept' => 'application/json; charset=UTF-8'],
        );
    }

    public function getUsers(int $userId): Response
    {
        return $this->inst->get(
            path: '/users/' . $userId,
            headers: [
                'Path-Tag' => '/users/{userId}',
                'Accept' => 'application/json; charset=UTF-8',
            ],
        );
    }

    public function getPosts(int $postId): Response
    {
        return $this->inst->get(
            path: '/posts/?' . $this->createQuery(\compact('postId')),
            headers: [
                'Accept' => 'application/json; charset=UTF-8',
            ],
        );
    }

    /**
     * @param array<string, mixed> $params
     * @return string
     */
    private function createQuery(array $params): string
    {
        $qs = [];
        foreach ($params as $key => $value) {
            if (!\is_string($value)) {
                throw new \RuntimeException('Invalid query string value=' . $value);
            }
            $qs[] = \sprintf('%s=%s', $key, \urlencode($value));
        }
        return \implode('&', $qs);
    }
}
