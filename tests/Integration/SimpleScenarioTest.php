<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Tests\Integration;

use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\RequestBuilder;
use Heavyrain\Tests\TestCase;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UriFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(RequestBuilder::class)]
#[CoversClass(Client::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&PsrClientInterface */
        $psrClient = $this->createMock(PsrClientInterface::class);
        $builder = new RequestBuilder(
            new UriFactory(),
            new StreamFactory(),
            new RequestFactory(),
            'http://localhost',
        );
        $response = $this->createMock(ResponseInterface::class);

        $psrClient->expects($this->once())
            ->method('sendRequest')
            ->withAnyParameters()
            ->willReturn($response);

        $cl = new Client($psrClient, $builder);

        $func = static function (ClientInterface $cl): void {
            $cl->get('/');
            $cl->waitMicroSec(1);
        };

        $func($cl);
    }
}
