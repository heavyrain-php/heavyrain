<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Client\BuzzClientInterface;
use Closure;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Executor\Middlewares\ProfilingMiddleware;
use Heavyrain\Executor\Middlewares\WaitSendRequestMiddleware;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\RequestBuilder;
use Heavyrain\Support\DefaultHttpBuilder;

/**
 * @todo delete
 */
final class BuzzClient
{
    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly Closure $scenarioFunction,
        private readonly HttpProfiler $profiler,
        private readonly ClientInterface $client,
    ) {
    }

    public function createClient(?BuzzClientInterface $buzzClient = null): ClientInterface
    {
        $builder = new DefaultHttpBuilder();
        $client = $builder->buildClient($buzzClient, [
            // TODO: should be configurable
            'allow_redirects' => false,
            // must be true for profiling
            'expose_curl_info' => true,
            'verify' => $this->config->sslVerify,
            'timeout' => $this->config->timeout,
        ]);
        $client->addMiddleware(new ProfilingMiddleware($this->profiler));
        $client->addMiddleware(new WaitSendRequestMiddleware($this->config->waitAfterSendRequestSec));

        return new Client(
            $client,
            new RequestBuilder(
                $builder->getUriFactory(),
                $builder->getStreamFactory(),
                $builder->getRequestFactory(),
                $this->config->baseUri,
            ),
        );
    }
}
