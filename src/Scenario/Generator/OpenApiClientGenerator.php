<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

use cebe\openapi\spec\OpenApi;
use RuntimeException;

/**
 * Generates OpenAPI-based ApiClient classes.
 */
final class OpenApiClientGenerator
{
    /**
     * Generates an ApiClient class from an OpenAPI schema.
     * @param OpenApi $openApiSchema
     * @return string ApiClient class file contents
     */
    public function generate(OpenApi $openApiSchema): string
    {
        $this->ensureSupportedVersion($openApiSchema->openapi);
        $methods = $this->generateMethods($openApiSchema);

        $clientStub = \file_get_contents(__DIR__ . '/Stubs/ApiClient.stub');

        return $this->generateFromStub(
            $openApiSchema,
            $methods,
            $clientStub,
        );
    }

    /**
     * Ensures that the OpenAPI version is supported.
     * @param null|string $version
     * @throws RuntimeException
     * @throws NotImplementedException
     */
    private function ensureSupportedVersion(?string $version): void
    {
        if (\is_null($version)) {
            throw new RuntimeException('OpenAPI schema must have a "version" key'); // @codeCoverageIgnore
        }

        if ($version === '2.0.0') {
            throw new NotImplementedException('OpenAPI 2.0.0 is not supported yet');
        }

        if ($version === '3.1.0') {
            throw new NotImplementedException('OpenAPI 3.1.0 is not supported yet');
        }

        if ($version !== '3.0.0' && $version !== '3.0.1' && $version !== '3.0.2' && $version !== '3.0.3') {
            throw new RuntimeException('Unsupported OpenAPI version: ' . $version);
        }
    }

    /**
     * Generates methods
     * @param OpenApi $openApiSchema
     * @return ApiClientMethod[]
     */
    private function generateMethods(OpenApi $openApiSchema): array
    {
        $methods = [];
        foreach ($openApiSchema->paths as $path => $pathItem) {
            \assert(\is_string($path));
            foreach ($pathItem->getOperations() as $method => $operation) {
                \assert(\is_string($method));
                $methods[] = new ApiClientMethod(
                    $path,
                    $method,
                    $operation,
                );
            }
        }

        return $methods;
    }

    /**
     * Generates ApiClient class file contents from stubs.
     * @param OpenApi $openApiSchema
     * @param array $methods
     * @param string $clientStub
     * @return string
     */
    private function generateFromStub(OpenApi $openApiSchema, array $methods, string $clientStub): string
    {

    }
}
