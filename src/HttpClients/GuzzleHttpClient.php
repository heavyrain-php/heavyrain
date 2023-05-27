<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\HttpClients;

use GuzzleHttp\Client;
use Heavyrain\HttpClientInterface;

class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }
}
