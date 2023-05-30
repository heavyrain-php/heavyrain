<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Response;

final class ScenarioClient
{
    public function __construct(public readonly InstructorInterface $inst)
    {
    }

    /**
     * GET /
     *
     * @return Response
     */
    public function getIndex(): Response
    {
        return $this->inst->get('/');
    }

    /**
     * POST /json
     *
     * @param array{a: string} $body
     * @return Response
     */
    public function postJson(array $body): Response
    {
        return $this->inst->postJson(
            path: '/json',
            body: $body,
        );
    }

    /**
     * GET /users/{userId}
     *
     * @param integer $userId
     * @return Response
     */
    public function getUsers(int $userId): Response
    {
        return $this->inst->getJson(
            path: '/users/' . $userId,
            headers: [
                'Path-Tag' => '/users/{userId}',
            ],
        );
    }

    /**
     * GET /posts/?postId=
     *
     * @param integer $postId
     * @return Response
     */
    public function getPosts(int $postId): Response
    {
        return $this->inst->getJson(
            path: '/posts/?' . $this->createQuery(\compact('postId')),
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
                throw new \RuntimeException(\sprintf('Invalid query string value=%s', (string)$value));
            }
            $qs[] = \sprintf('%s=%s', $key, \urlencode($value));
        }
        return \implode('&', $qs);
    }
}
