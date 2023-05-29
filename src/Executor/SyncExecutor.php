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
        $client = $builder->buildClient(null, [
            'allow_redirects' => false,
            'verify' => $config->sslVerify,
            'timeout' => $config->timeout,
            'expose_curl_info' => true,
        ]);
        $this->inst = new PsrInstructor(
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
        $request = $builder->getRequestFactory()->createRequest('GET', $this->config->baseUri);
        foreach ($this->config->defaultHeaders as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        if (!$request->hasHeader('Accept')) {
            $request = $request->withHeader('Accept', '*/*');
        }
        if (!$request->hasHeader('User-Agent')) {
            $request = $request->withHeader('User-Agent', 'heavyrain/0.0.1');
        }
        return $request;
    }
}
