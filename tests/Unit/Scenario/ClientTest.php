<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Heavyrain\Contracts\RequestBuilderInterface;
use Heavyrain\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Client\ClientInterface;

#[CoversClass(Client::class)]
final class ClientTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function testWait(): void
    {
        $cl = new Client($this->createMock(ClientInterface::class), $this->createMock(RequestBuilderInterface::class));

        $cl->waitSec(0.0001);
        $cl->waitMilliSec(0.1);
        $cl->waitMicroSec(1);
    }
}
