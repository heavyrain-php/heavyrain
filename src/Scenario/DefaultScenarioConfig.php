<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

final class DefaultScenarioConfig implements ScenarioConfigInterface
{
    public function getDefaultRequest(RequestFactoryInterface $requestFactory, string $baseUri): RequestInterface
    {
        return $requestFactory->createRequest('GET', $baseUri);
    }

    public function getScenarioName(): string
    {
        return 'Heavyrain Default';
    }
}
