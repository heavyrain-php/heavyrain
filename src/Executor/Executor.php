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
use SplQueue;

class Executor
{
    /** @var SplQueue<HttpResult> $profiles */
    private SplQueue $profiles;

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
        /** @var SplQueue<HttpResult> */
        $this->profiles = new SplQueue();
        $this->inst = new PsrInstructor(
            $builder->getRequestFactory()->createRequest('GET', $this->config->baseUri),
            $client,
        );
    }

    /** @return SplQueue<HttpResult> */
    public function getProfiles(): SplQueue
    {
        return $this->profiles;
    }

    public function execute(): void
    {
        try {
            $this->scenarioFunction->invoke($this->inst);
        } catch (\Throwable $e) {
            // TODO: Record last API is failed
        }
    }
}
