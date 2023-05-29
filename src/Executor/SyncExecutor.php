<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use Psr\Http\Message\RequestInterface;
use ReflectionFunction;

class SyncExecutor
{
    private InstructorInterface $inst;

    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly ReflectionFunction $scenarioFunction,
        private readonly HttpProfiler $profiler,
    ) {
        $builder = new DefaultHttpBuilder();
        // TODO: Override option from outside
        $client = $builder->buildClient(null, [
            'allow_redirects' => true,
            'expose_curl_info' => true,
            'verify' => $config->sslVerify,
            'timeout' => $config->timeout,
        ]);
        $this->inst = new PsrInstructor(
            $builder->getUriFactory(),
            $this->createDefaultRequest($builder),
            $client,
            $this->profiler,
        );
    }

    public function execute(): void
    {
        try {
            $this->scenarioFunction->invoke($this->inst);
        } catch (\Throwable $e) {
            $this->profiler->profileException($e);
        }
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
