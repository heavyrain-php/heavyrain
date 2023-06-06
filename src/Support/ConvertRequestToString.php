<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Support;

use Psr\Http\Message\RequestInterface;
use Stringable;

/**
 * Converts RequestInterface to string
 */
final class ConvertRequestToString implements Stringable
{
    public function __construct(
        private readonly RequestInterface $request,
    ) {
    }

    /**
     * Converts RequestInterface to string for testing
     *
     * @return string
     * @psalm-return non-empty-string
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Converts RequestInterface to string for testing
     *
     * @return string
     * @psalm-return non-empty-string
     */
    public function __invoke(): string
    {
        return $this->__toString();
    }

    /**
     * Converts RequestInterface to string for testing
     *
     * @return string
     * @psalm-return non-empty-string
     * @link https://github.com/guzzle/psr7/blob/2.5.0/src/Message.php#L18
     */
    public function __toString(): string
    {
        $req = $this->request;
        $str = '';

        $str .= \trim($req->getMethod() . ' ' . $req->getRequestTarget() . ' HTTP/' . $req->getProtocolVersion());
        if (!$req->hasHeader('Host')) {
            $str .= "\r\n" . 'Host: ' . $req->getUri()->getHost();
        }

        foreach ($req->getHeaders() as $name => $values) {
            $str .= "\r\n" . $name . ': ' . \implode(', ', $values);
        }

        $str .= "\r\n\r\n" . $req->getBody()->__toString();

        \assert(\strlen($str) > 0);

        /** @var non-empty-string */
        return $str;
    }
}
