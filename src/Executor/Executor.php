<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Heavyrain\Scenario\InstructorInterface;
use Heavyrain\Scenario\Instructors\PsrInstructor;
use Heavyrain\Support\DefaultHttpBuilder;
use ReflectionFunction;
use SplStack;

class Executor
{
    /** @var SplStack<HttpResult> $profiles */
    private SplStack $profiles;

    private InstructorInterface $inst;

    public function __construct(
        private readonly ExecutorConfig $config,
        private readonly ReflectionFunction $scenarioFunction,
    ) {
        $builder = new DefaultHttpBuilder();
        $client = $builder->buildClient(null, [
            'allow_redirects' => false,
            'verify' => $config->sslVerify,
            'timeout' => $config->timeout,
            'expose_curl_info' => true,
        ]);
        /** @var SplStack<HttpResult> */
        $this->profiles = new SplStack();
        $client->addMiddleware(new HttpRequestProfilerMiddleware($this->profiles));
        $this->inst = new PsrInstructor(
            $builder->getRequestFactory(),
            $builder->getStreamFactory(),
            $client,
            $this->config->baseUri,
        );
    }

    /** @return SplStack<HttpResult> */
    public function getProfiles(): SplStack
    {
        return $this->profiles;
    }

    public function execute(): void
    {
        try {
            $this->scenarioFunction->invoke($this->inst);
        } catch (\Throwable $e) {
            // Do nothing
        }
    }
}
