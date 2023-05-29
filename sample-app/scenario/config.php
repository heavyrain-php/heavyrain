<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Heavyrain\Scenario\ScenarioConfigInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

return static function (): ScenarioConfigInterface {
    return new class () implements ScenarioConfigInterface {
        public function getDefaultRequest(RequestFactoryInterface $requestFactory, string $baseUri): RequestInterface
        {
            return $requestFactory->createRequest('GET', $baseUri);
        }

        public function getScenarioName(): string
        {
            return 'Heavyrain Sample';
        }
    };
};
