<?php

declare(strict_types=1);

/**
 * @license MIT
 */

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
final class ClientFactory
{
    /**
     * Creates a new HTTP client instance with the specified base URI.
     *
     * @param string $baseUri The base URI for the HTTP client.
     * @return ClientInterface The newly created HTTP client instance.
     */
    public static function create(string $baseUri): ClientInterface
    {
        $ampHttpClient = (new HttpClientBuilder())
            ->allowDeprecatedUriUserInfo()
            ->followRedirects(0)
            ->skipAutomaticCompression()
            ->skipDefaultAcceptHeader()
            ->skipDefaultUserAgent()
            ->build();

        $httpClient = new AmphpClient(
            $ampHttpClient,
            new ResponseFactory(),
        );

        $requestBuilder = new RequestBuilder(
            new UriFactory(),
            new StreamFactory(),
            new RequestFactory(),
            $baseUri,
        );

        return new Client($httpClient, $requestBuilder);
    }
}
