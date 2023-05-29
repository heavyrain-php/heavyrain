<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Executor;

use Buzz\Exception\NetworkException;
use Buzz\Exception\RequestException;
use Buzz\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use SplQueue;

class HttpRequestProfilerMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SplQueue $profiles)
    {
    }

    /** @psalm-suppress MissingReturnType */
    public function handleRequest(RequestInterface $request, callable $next)
    {
        try {
            // Do nothing
        } catch (RequestException $e) {
            // Handle request fail
            $this->profiles->enqueue(new HttpResult(
                false,
                \sprintf('Failed to request method=%s uri=%s', $e->getRequest()->getMethod(), $e->getRequest()->getUri()->__toString()),
                $request,
                null,
                $e,
                null,
            ));
        } catch (NetworkException $e) {
            // Handle network error
            $this->profiles->enqueue(new HttpResult(
                false,
                'NetworkError',
                $request,
                null,
                $e,
                null,
            ));
        } catch (\Throwable $e) {
            // Handle unknown error
            $this->profiles->enqueue(new HttpResult(
                false,
                \sprintf('UnknownError class=%s', \get_class($e)),
                $request,
                null,
                $e,
                null,
            ));
        } finally {
            return $next($request);
        }
    }

    /** @psalm-suppress MissingReturnType */
    public function handleResponse(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $curlInfo = null;

        try {
            if (!$response->hasHeader('__curl_info')) {
                // Handle failed to get curl info
                throw new RuntimeException('Undefined header __curl_info', 1);
            }

            /** @var array|bool|null $curlInfo */
            $curlInfo = \json_decode($response->getHeaderLine('__curl_info'), true, 512, \JSON_UNESCAPED_UNICODE);
            if (is_null($curlInfo) || !\is_array($curlInfo) || \json_last_error() !== \JSON_ERROR_NONE) {
                // Handle failed to get curl info
                $message = \sprintf(
                    'Failed to get curl_info message=%s',
                    \json_last_error_msg(),
                );
                throw new RuntimeException($message, 2);
            }

            $this->profiles->enqueue(new HttpResult(
                true,
                'Ok',
                $request,
                $response,
                null,
                $curlInfo,
            ));
        } catch (\Throwable $e) {
            $this->profiles->enqueue(new HttpResult(
                true,
                $e->getMessage(),
                $request,
                $response,
                $e,
                $curlInfo,
            ));
        } finally {
            return $next($request, $response);
        }
    }
}
