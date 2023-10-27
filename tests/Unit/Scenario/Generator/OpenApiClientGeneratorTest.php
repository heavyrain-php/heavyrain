<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Generator;

use cebe\openapi\Reader;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OpenApiClientGenerator::class)]
final class OpenApiClientGeneratorTest extends TestCase
{
    #[Test]
    public function testGenerate(): void
    {
        $openapi = Reader::readFromYamlFile(__DIR__ . '/../../../Stubs/petstore3.0.0.0.yaml');

        $actual = (new OpenApiClientGenerator())->generate($openapi);

        self::assertSame(\file_get_contents(__DIR__ . '/../../../Stubs/PetStoreApiClient.actual'), $actual);
    }
}
