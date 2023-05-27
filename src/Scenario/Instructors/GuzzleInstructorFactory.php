<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Scenario\Instructors;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PsrInstructor factory using Guzzle HTTP
 */
class GuzzleInstructorFactory
{
    /**
     * Creates PsrInstructor with Guzzle HTTP library
     *
     * @param Client $client
     * @return PsrInstructor
     */
    public static function create(Client $client): PsrInstructor
    {
        $requestFactory = new class () implements RequestFactoryInterface {
            public function createRequest(string $method, $uri): RequestInterface
            {
                return new Request($method, $uri);
            }
        };
        $streamFactory = new class () implements StreamFactoryInterface {
            public function createStream(string $content = ''): StreamInterface
            {
                return Utils::streamFor($content);
            }
            public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
            {
                return $this->createStreamFromResource(Utils::tryFopen($filename, $mode));
            }
            public function createStreamFromResource($resource): StreamInterface
            {
                return Utils::streamFor($resource);
            }
        };

        return new PsrInstructor(
            $requestFactory,
            $streamFactory,
            $client,
        );
    }
}
