<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Tests\Integration;

use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Contracts\HttpClientInterface;
use Heavyrain\Scenario\AssertableResponse;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\RequestBuilder;
use Heavyrain\Tests\TestCase;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UriFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(AssertableResponse::class)]
#[CoversClass(RequestBuilder::class)]
#[CoversClass(Client::class)]
final class SimpleScenarioTest extends TestCase
{
    #[Test]
    public function run_simple_scenario(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&HttpClientInterface */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $builder = new RequestBuilder(
            new UriFactory(),
            new StreamFactory(),
            new RequestFactory(),
            'http://localhost',
        );
        $response = $this->createMock(ResponseInterface::class);

        $httpClient->expects($this->once())
            ->method('sendRequest')
            ->withAnyParameters()
            ->willReturn($response);

        $cl = new Client($httpClient, $builder);

        $func = static function (ClientInterface $cl): void {
            $cl->get('/');
            $cl->waitMicroSec(1);
        };

        $func($cl);
    }
}
