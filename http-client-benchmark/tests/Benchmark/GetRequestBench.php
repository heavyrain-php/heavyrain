<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use GuzzleHttp\Client;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

#[BeforeClassMethods('ensureTargetIsAlive')]
class GetRequestBench
{
    protected ?ClientInterface $psrClient = null;
    protected ?\React\Http\Io\Transaction $reactClient = null;
    protected ?RequestInterface $psrRequest = null;
    protected ?\Amp\Http\Client\HttpClient $ampClient = null;
    protected ?\Amp\Http\Client\Request $ampRequest = null;
    protected ?\Laminas\Http\Client $laminasClient = null;
    protected ?\Laminas\Http\Request $laminasRequest = null;
    protected ?\Symfony\Contracts\HttpClient\HttpClientInterface $symfonyClient = null;

    public function tearDownProperty(): void
    {
        $this->psrClient = null;
        $this->reactClient = null;
        $this->psrRequest = null;
        $this->ampClient = null;
        $this->ampRequest = null;
        $this->laminasClient = null;
        $this->laminasRequest = null;
        $this->symfonyClient = null;
    }

    /**
     * Retrieve target URI
     *
     * @return string
     */
    protected static function getTargetUri(): string
    {
        $uri = $_ENV['TARGET_URI'] ?? 'http://localhost:8081';
        \assert(\is_string($uri) && \str_starts_with($uri, 'http'));

        return $uri;
    }

    public static function ensureTargetIsAlive(): void
    {
        $uri = self::getTargetUri();

        $result = \file_get_contents($uri);

        if ($result === false) {
            throw new \RuntimeException(\sprintf('Target URI %s did not return 200 response', $uri), 1);
        }
    }

    public function providePsrRequest(): array
    {
        return [
            'guzzle' => ['request' => 'guzzle'],
            'nyholm' => ['request' => 'nyholm'],
            'diactoros' => ['request' => 'diactoros'],
        ];
    }

    protected function createPsrRequest(
        string $type,
        string $uri,
        string $method = 'GET',
        mixed $body = null,
        array $headers = [],
        string $version = '1.1',
    ): RequestInterface {
        return match ($type) {
            'guzzle' => new \GuzzleHttp\Psr7\Request($method, $uri, $headers, $body, $version),
            'nyholm' => new \Nyholm\Psr7\Request($method, $uri, $headers, $body, $version),
            'diactoros' => (new \Laminas\Diactoros\Request($uri, $method, $body ?? 'php://temp', $headers))->withProtocolVersion('1.1'),
        };
    }

    public function provideGuzzleClient(): array
    {
        return [
            'curl' => ['client' => 'curl'],
            'curl_multi' => ['client' => 'curl_multi'],
            'stream' => ['client' => 'stream'],
        ];
    }

    protected function createGuzzleClient(string $type, array $defaultOptions = []): ClientInterface
    {
        $handler = match ($type) {
            'curl' => new \GuzzleHttp\Handler\CurlHandler(),
            'curl_multi' => new \GuzzleHttp\Handler\CurlMultiHandler(),
            'stream' => new \GuzzleHttp\Handler\StreamHandler(),
        };

        return new Client([...$defaultOptions, ...['handler' => \GuzzleHttp\HandlerStack::create($handler)]]);
    }

    /**
     * Set up Guzzle
     *
     * @param array{client: string, request: string} $params
     * @return void
     */
    public function setUpGuzzle(array $params): void
    {
        /** @var string $clientName */
        $clientName = $params['client'];
        /** @var string $requestName */
        $requestName = $params['request'];
        $uri = self::getTargetUri();

        $this->psrClient = $this->createGuzzleClient($clientName);
        $this->psrRequest = $this->createPsrRequest($requestName, $uri);
    }

    /**
     * Tests Guzzle client
     *
     * @param array{client: string, request: string} $params
     * @return void
     */
    #[BeforeMethods('setUpGuzzle')]
    #[AfterMethods('tearDownProperty')]
    #[ParamProviders(['provideGuzzleClient', 'providePsrRequest'])]
    #[Groups(['psr', 'guzzle'])]
    public function benchGuzzle(array $params): void
    {
        \assert($this->psrClient instanceof ClientInterface);
        \assert($this->psrRequest instanceof RequestInterface);
        $response = $this->psrClient->sendRequest($this->psrRequest);
        \assert($response->getStatusCode() === 200);
    }

    public function providePsrResponseFactory(): array
    {
        return [
            'guzzle' => ['responseFactory' => 'guzzle'],
            'nyholm' => ['responseFactory' => 'nyholm'],
            'diactoros' => ['responseFactory' => 'diactoros'],
        ];
    }

    protected function createPsrResponseFactory(string $type): ResponseFactoryInterface
    {
        return match ($type) {
            'guzzle' => new \GuzzleHttp\Psr7\HttpFactory(),
            'nyholm' => new \Nyholm\Psr7\Factory\Psr17Factory(),
            'diactoros' => new \Laminas\Diactoros\ResponseFactory(),
        };
    }

    public function provideBuzzClient(): array
    {
        return [
            'curl' => ['client' => 'curl'],
            'multiCurl' => ['client' => 'multiCurl'],
            'fileGetContents' => ['client' => 'fileGetContents'],
        ];
    }

    protected function createBuzzClient(string $type, ResponseFactoryInterface $responseFactory, array $options = []): ClientInterface
    {
        return match ($type) {
            'curl' => new \Buzz\Client\Curl($responseFactory, $options),
            'multiCurl' => new \Buzz\Client\MultiCurl($responseFactory, $options),
            'fileGetContents' => new \Buzz\Client\FileGetContents($responseFactory, $options),
        };
    }

    /**
     * @param array{client: string, request: string, responseFactory: string} $params
     * @return void
     */
    public function setUpBuzz(array $params): void
    {
        /** @var string $clientName */
        $clientName = $params['client'];
        /** @var string $requestName */
        $requestName = $params['request'];
        $uri = self::getTargetUri();
        /** @var string $responseFactoryName */
        $responseFactoryName = $params['responseFactory'];

        $psrResponseFactory = $this->createPsrResponseFactory($responseFactoryName);
        $this->psrClient = $this->createBuzzClient($clientName, $psrResponseFactory);
        $this->psrRequest = $this->createPsrRequest($requestName, $uri);
    }

    #[BeforeMethods('setUpBuzz')]
    #[AfterMethods('tearDownProperty')]
    #[ParamProviders(['provideBuzzClient', 'providePsrRequest', 'providePsrResponseFactory'])]
    #[Groups(['psr', 'buzz'])]
    public function benchBuzz(array $params): void
    {
        \assert($this->psrClient instanceof ClientInterface);
        \assert($this->psrRequest instanceof RequestInterface);
        $response = $this->psrClient->sendRequest($this->psrRequest);
        \assert($response->getStatusCode() === 200);
    }

    public function setUpReact(array $params): void
    {
        /** @var string $requestName */
        $requestName = $params['request'];
        $uri = self::getTargetUri();
        $this->psrRequest = $this->createPsrRequest($requestName, $uri);
        $loop = \React\EventLoop\Loop::get();
        $this->reactClient = new \React\Http\Io\Transaction(
           \React\Http\Io\Sender::createFromLoop($loop),
           $loop,
        );
    }

    #[BeforeMethods('setUpReact')]
    #[AfterMethods('tearDownProperty')]
    #[ParamProviders('providePsrRequest')]
    #[Groups(['psr', 'react'])]
    public function benchReact(): void
    {
        \assert($this->reactClient instanceof \React\Http\Browser);
        \assert($this->psrRequest instanceof RequestInterface);
        $response = \React\Async\await($this->reactClient->send($this->psrRequest));
        \assert($response instanceof ResponseInterface);
        \assert($response->getStatusCode() === 200);
    }

    public function setUpAmphp(): void
    {
        $uri = self::getTargetUri();
        $this->ampClient = \Amp\Http\Client\HttpClientBuilder::buildDefault();
        $this->ampRequest = new \Amp\Http\Client\Request($uri);
    }

    #[BeforeMethods('setUpAmphp')]
    #[AfterMethods('tearDownProperty')]
    #[Groups(['non-psr', 'amphp'])]
    public function benchAmphp(): void
    {
        \assert($this->ampClient instanceof \Amp\Http\Client\HttpClient);
        \assert($this->ampRequest instanceof \Amp\Http\Client\Request);
        $promise = $this->ampClient->request($this->ampRequest);
        $response = \Amp\Promise\wait($promise);
        \assert($response instanceof \Amp\Http\Client\Response);
        \assert($response->getStatus() === 200);
    }

    public function setUpLaminas(): void
    {
        $uri = self::getTargetUri();
        $this->laminasClient = new \Laminas\Http\Client();
        $this->laminasRequest = new \Laminas\Http\Request();
        $this->laminasRequest->setUri($uri);
    }

    #[BeforeMethods('setUpLaminas')]
    #[AfterMethods('tearDownProperty')]
    #[Groups(['non-psr', 'laminas'])]
    public function benchLaminas(): void
    {
        \assert($this->laminasClient instanceof \Laminas\Http\Client);
        \assert($this->laminasRequest instanceof \Laminas\Http\Request);
        $response = $this->laminasClient->send($this->laminasRequest);
        \assert($response->getStatusCode() === 200);
    }

    public function provideSymfonyClient(): array
    {
        return [
            'native' => ['client' => 'native'],
            'curl' => ['client' => 'curl'],
        ];
    }

    public function setUpSymfony(array $params): void
    {
        $clientName = $params['client'];
        \assert(is_string($clientName));
        $this->symfonyClient = match ($clientName) {
            'native' => new \Symfony\Component\HttpClient\NativeHttpClient(),
            'curl' => new \Symfony\Component\HttpClient\CurlHttpClient(),
        };
    }

    #[BeforeMethods('setUpSymfony')]
    #[AfterMethods('tearDownProperty')]
    #[ParamProviders('provideSymfonyClient')]
    #[Groups(['non-psr', 'symfony'])]
    public function benchSymfony(): void
    {
        \assert($this->symfonyClient instanceof \Symfony\Contracts\HttpClient\HttpClientInterface);
        $uri = self::getTargetUri();
        $response = $this->symfonyClient->request('GET', $uri);
        \assert($response->getStatusCode() === 200);
    }
}
