<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

interface ScenarioConfigInterface
{
    public function getDefaultRequest(RequestFactoryInterface $requestFactory, string $baseUri): RequestInterface;

    /**
     * Get scenario name for logging
     *
     * @return string
     */
    public function getScenarioName(): string;
}
