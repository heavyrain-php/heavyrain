<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\Schema;
use Stringable;

/**
 * Api client method definition
 */
final class ApiClientMethod implements Stringable
{
    private const STUB = <<<'EOL'
    public function {{ methodName }}({{ parameters }}): AssertableResponseInterface
    {
        return $this->client->requestWithOptions({{ options }});
    }

EOL;

    /**
     * @var array $pathArgs
     * @psalm-var array<string, array{type: string, name: string, required: bool}> $pathArgs
     */
    private readonly array $pathArgs;
    /**
     * @var array $query
     * @psalm-var array<string, array{type: string, name: string, required: bool}> $query
     */
    private readonly array $query;
    private readonly ?string $body;
    private readonly ?array $json;

    /**
     * @var array $pathArgs
     * @psalm-var array<string, array{type: string, name: string, required: bool}> $pathArgs
     */
    private readonly array $parameters;

    public function __construct(
        public readonly string $path,
        public readonly string $method,
        public readonly Operation $operation,
        public readonly bool $assertsOk = true,
    ) {
        $pathArgs = [];
        $parameters = [];
        $query = [];
        $this->body = null;
        $this->json = null;

        // Add from parameters
        foreach ($this->operation->parameters as $parameter) {
            \assert($parameter instanceof Parameter);

            if ($parameter->in === 'header' || $parameter->in === 'cookie') {
                // TODO: implementation
                continue;
            }

            \assert($parameter->schema instanceof Schema || \is_null($parameter->schema));
            /** @var ?bool $required */
            $required = $parameter->required;
            $required = $parameter->in === 'path' ? true : ($required ?? false);

            $type = [
                'type' => $this->parseType($parameter->schema?->type),
                'name' => $parameter->name,
                'required' => $required,
            ];

            // Add pathArgs
            if ($parameter->in === 'path') {
                \assert($parameter->schema instanceof Schema || \is_null($parameter->schema));
                $pathArgs[$parameter->name] = $type;
                $parameters[$parameter->name] = $type;
            }

            // Add query
            if ($parameter->in === 'query') {
                \assert($parameter->schema instanceof Schema || \is_null($parameter->schema));
                $query[$parameter->name] = $type;
                $parameters[$parameter->name] = $type;
            }
        }
        $this->pathArgs = $pathArgs;
        $this->query = $query;
        $this->parameters = $parameters;

        // Add from requestBody
    }

    public function __toString(): string
    {
        return \str_replace([
            '{{ methodName }}',
            '{{ parameters }}',
            '{{ options }}',
        ], [
            $this->getMethodName(),
            $this->getParameters(),
            $this->getOptions(),
        ], self::STUB);
    }

    private function parseType(?string $type): string
    {
        return match($type) {
            'string' => 'string',
            'integer' => 'int',
            'number' => 'float',
            'boolean' => 'bool',
            'array' => 'array',
            default => 'mixed',
        };
    }

    private function getMethodName(): string
    {
        /** @var ?string $name operationId must be, but some definition may not have one */
        $name = $this->operation->operationId;

        if (\is_null($name) || '' === $name) {
            // suggest operationId(method + Path)
            $name = \lcfirst($this->method) . \str_replace(['{', '}', '/'], '', \ucwords($this->path, '/'));
        }

        // to camelCase
        $name = \lcfirst(\str_replace([' ', '_', '.', '-'], '', \ucwords($name, ' _.-')));

        // is valid PHP methodName?
        if (false === \preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name)) {
            throw new \RuntimeException(\sprintf('Invalid operationId(format is not supported): %s', $name));
        }

        // TODO: except reserved methodName

        return $name;
    }

    private function getParameters(): string
    {
        return \implode(', ', \array_map(function (array $parameter): string {
            return \sprintf('%s $%s', $parameter['type'], $parameter['name']);
        }, $this->parameters));
    }

    private function getOptions(): string
    {
        return \sprintf(
            "method: '%s', path: '%s', pathArgs: %s, query: %s, body: %s, json: %s, assertsOk: %s",
            $this->getMethod(),
            $this->getPath(),
            $this->getPathArgs(),
            $this->getQuery(),
            $this->getBody(),
            $this->getJson(),
            $this->getAssertsOk(),
        );
    }

    private function getMethod(): string
    {
        return $this->method;
    }

    private function getPath(): string
    {
        return $this->path;
    }

    private function getPathArgs(): string
    {
        if (\count($this->pathArgs) === 0) {
            return 'null';
        }

        $results = [];
        foreach ($this->pathArgs as $name => $arg) {
            $results[] = \sprintf("'%s' => $%s", $name, $arg['name']);
        }

        return '[' . \implode(', ', $results) . ']';
    }

    private function getQuery(): string
    {
        if (\count($this->query) === 0) {
            return 'null';
        }

        $results = [];
        foreach ($this->query as $name => $arg) {
            $results[] = \sprintf("'%s' => $%s", $name, $arg['name']);
        }

        return '[' . \implode(', ', $results) . ']';
    }

    private function getBody(): string
    {
        return 'null';
    }

    private function getJson(): string
    {
        return 'null';
    }

    private function getAssertsOk(): string
    {
        return $this->assertsOk ? 'true' : 'false';
    }
}
