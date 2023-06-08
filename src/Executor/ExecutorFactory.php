<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Client\BuzzClientInterface;
use Closure;
use Heavyrain\Contracts\ClientInterface;
use Heavyrain\Contracts\ExecutorInterface;
use Heavyrain\Scenario\Client;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\RequestBuilder;
use Heavyrain\Support\DefaultHttpBuilder;

final class ExecutorFactory
{
    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly Closure $scenarioFunction,
        private readonly HttpProfiler $profiler,
    ) {
    }

    public function createSync(?BuzzClientInterface $buzzClient = null): ExecutorInterface
    {
        return new SyncExecutor(
            $this->config,
            $this->scenarioFunction,
            $this->profiler,
            $this->createClient($buzzClient),
        );
    }

    private function createClient(?BuzzClientInterface $buzzClient = null): ClientInterface
    {
        $builder = new DefaultHttpBuilder();
        $client = $builder->buildClient($buzzClient, [
            // TODO: should be configurable
            'allow_redirects' => true,
            // must be true for profiling
            'expose_curl_info' => true,
            'verify' => $this->config->sslVerify,
            'timeout' => $this->config->timeout,
        ]);
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
