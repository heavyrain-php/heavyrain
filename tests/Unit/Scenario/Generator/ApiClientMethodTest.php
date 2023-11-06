<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

use cebe\openapi\spec\Operation;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ApiClientMethodTest::class)]
final class ApiClientMethodTest extends TestCase
{
    #[Test]
    #[DataProvider('provideTestToString')]
    public function testToString(
        string $path,
        string $method,
        Operation $operation,
        bool $assertsOk,
        string $expected,
    ): void {
        $actual = new ApiClientMethod($path, $method, $operation, $assertsOk);

        self::assertSame($expected, $actual->__toString());
    }

    public static function provideTestToString(): array
    {
        return [
            'minimal get /' => [
                'path' => '/',
                'method' => 'get',
                'operation' => new Operation([]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function get(): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/', pathArgs: null, query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'minimal post /' => [
                'path' => '/',
                'method' => 'post',
                'operation' => new Operation([]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function post(): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'post', path: '/', pathArgs: null, query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'minimal get /getRoot' => [
                'path' => '/a',
                'method' => 'get',
                'operation' => new Operation([
                    'operationId' => 'getRoot',
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function getRoot(): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/a', pathArgs: null, query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'minimal get /get_b' => [
                'path' => '/b',
                'method' => 'get',
                'operation' => new Operation([
                    'operationId' => 'get_b',
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function getB(): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/b', pathArgs: null, query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'get /items/{id}' => [
                'path' => '/items/{id}',
                'method' => 'get',
                'operation' => new Operation([
                    'operationId' => 'getItem',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => [
                                'type' => 'integer',
                            ],
                        ],
                    ],
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function getItem(int $id): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/items/{id}', pathArgs: ['id' => $id], query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'get /items/{id}/orders/{orderId}' => [
                'path' => '/items/{id}/orders/{orderId}',
                'method' => 'get',
                'operation' => new Operation([
                    'operationId' => 'getItemOrder',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => [
                                'type' => 'integer',
                            ],
                        ],
                        [
                            'name' => 'orderId',
                            'in' => 'path',
                            'required' => true,
                        ],
                    ],
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function getItemOrder(int $id, mixed $orderId): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/items/{id}/orders/{orderId}', pathArgs: ['id' => $id, 'orderId' => $orderId], query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'get query /weapon?id=1' => [
                'path' => '/weapon',
                'method' => 'get',
                'operation' => new Operation([
                    'operationId' => 'getWeapon',
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'query',
                            'required' => true,
                            'schema' => [
                                'type' => 'integer',
                            ],
                        ],
                    ],
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function getWeapon(int $id): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/weapon', pathArgs: null, query: ['id' => $id], body: null, json: null, assertsOk: true);
    }

EOL
            ],
            'post query /todo/add' => [
                'path' => '/todo/add',
                'method' => 'post',
                'operation' => new Operation([
                    'operationId' => 'AddTodo',
                    'requestBody' => [
                        'content' => [
                            'application/x-www-form-urlencoded' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => [
                                            'type' => 'string',
                                        ],
                                        'description' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                    'required' => [
                                        'title',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function addTodo(string $body): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'post', path: '/todo/add', pathArgs: null, query: null, body: $body, json: null, assertsOk: true);
    }

EOL
            ],
            'post query /todo/update' => [
                'path' => '/todo/update',
                'method' => 'post',
                'operation' => new Operation([
                    'operationId' => 'UpdateTodo',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'title' => [
                                            'type' => 'string',
                                        ],
                                        'description' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                    'required' => [
                                        'title',
                                        'description',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    public function updateTodo(array $body): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'post', path: '/todo/update', pathArgs: null, query: null, body: null, json: $body, assertsOk: true);
    }

EOL
            ],
            'has Description get /' => [
                'path' => '/',
                'method' => 'get',
                'operation' => new Operation([
                    'summary' => 'Get root',
                    'description' => <<<'EOL'
Get parameters from root
2nd line
EOL
,
                ]),
                'assertsOk' => true,
                'expected' => <<<'EOL'
    /**
     * Get root
     *
     * Get parameters from root 2nd line
     */
    public function get(): AssertableResponseInterface
    {
        return $this->client->requestWithOptions(method: 'get', path: '/', pathArgs: null, query: null, body: null, json: null, assertsOk: true);
    }

EOL
            ],
        ];
    }
}
