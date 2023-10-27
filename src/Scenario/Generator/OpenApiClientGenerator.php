<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

use RuntimeException;

/**
 * Generates OpenAPI-based ApiClient classes.
 */
final class OpenApiClientGenerator
{
    /**
     * Generates an ApiClient class from an OpenAPI schema.
     * @param array $openApiSchema Array parsed Schema
     * @return string ApiClient class file contents
     */
    public function generate(array $openApiSchema): string
    {
        $this->ensureSupportedVersion(\array_key_exists('version', $openApiSchema) ? $openApiSchema['version'] : null);
        $methods = $this->generateMethods($openApiSchema);

        // TODO: map methods using stubs

        // TODO: inject methods to stub file contents

        // TODO: returns stub contents
    }

    /**
     * Generates methods
     * @param array $openApiSchema
     * @return ApiClientMethod[]
     */
    private function generateMethods(array $openApiSchema): array
    {
        // TODO: map paths to ApiClientMethod[]

        return [];
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

        if ($version !== '3.0.0') {
            throw new RuntimeException('Unsupported OpenAPI version: ' . $version); // @codeCoverageIgnore
        }
    }
}
