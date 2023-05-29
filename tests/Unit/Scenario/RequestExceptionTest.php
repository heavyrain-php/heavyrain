<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Tests\TestCase;
use Laminas\Diactoros\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(RequestException::class)]
final class RequestExceptionTest extends TestCase
{
    #[Test]
    public function testConstructor(): void
    {
        $request = new Request();
        $response = null;
        $previous = new \RuntimeException();

        $e = new RequestException($request, $response, $previous);

        self::assertSame($request, $e->getRequest());
        self::assertSame($response, $e->getResponse());
        self::assertSame($previous, $e->getPrevious());
    }
}
