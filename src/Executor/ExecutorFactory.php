<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Client\BuzzClientInterface;
use Closure;
use Heavyrain\Scenario\ExecutorInterface;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use Psr\Http\Message\RequestInterface;

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
            $this->createInstructor($buzzClient),
        );
    }

    private function createInstructor(?BuzzClientInterface $buzzClient = null): InstructorInterface
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

        return new PsrInstructor(
            $builder->getUriFactory(),
            $this->createDefaultRequest($builder),
            $client,
            $this->profiler,
        );
    }

    private function createDefaultRequest(DefaultHttpBuilder $builder): RequestInterface
    {
        $request = $this
            ->config
            ->scenarioConfig
            ->getDefaultRequest(
                $builder->getRequestFactory(),
                $this->config->baseUri,
            );

        if (!$request->hasHeader('Accept')) {
            $request = $request->withHeader('Accept', '*/*');
        }
        $request = $request->withHeader(
            'User-Agent',
            \sprintf(
                '%s %s',
                $this->config->userAgentBase,
                $this->config->scenarioConfig->getScenarioName(),
            ),
        );
        return $request;
    }
}
