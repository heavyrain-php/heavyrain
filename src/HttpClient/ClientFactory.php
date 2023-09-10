<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClient;

use Amp\Http\Client\HttpClientBuilder;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\RequestBuilder;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UriFactory;

/**
 * A factory for creating HTTP clients.
 */
class ClientFactory
{
    /**
     * @param HttpProfiler $profiler The HTTP profiler.
     * @param string $baseUri The base URI for the HTTP client.
     * @param int $followRedirects Follows redirects count
     */
    public function __construct(
        public readonly HttpProfiler $profiler,
        private readonly string $baseUri,
        private readonly int $followRedirects = 0,
    ) {
    }

    /**
     * Creates a new HTTP client instance with the specified base URI.
     *
     * @return ClientInterface The newly created HTTP client instance.
     */
    public function create(): ClientInterface
    {
        $ampHttpClient = (new HttpClientBuilder())
            ->allowDeprecatedUriUserInfo()
            ->followRedirects($this->followRedirects)
            ->skipAutomaticCompression()
            ->skipDefaultAcceptHeader()
            ->skipDefaultUserAgent()
            ->listen(new AmphpEventListener())
            ->build();

        $httpClient = new AmphpClient(
            $this->profiler,
            $ampHttpClient,
            new ResponseFactory(),
        );

        $requestBuilder = new RequestBuilder(
            new UriFactory(),
            new StreamFactory(),
            new RequestFactory(),
            $this->baseUri,
        );

        return new Client($httpClient, $requestBuilder);
    }
}
