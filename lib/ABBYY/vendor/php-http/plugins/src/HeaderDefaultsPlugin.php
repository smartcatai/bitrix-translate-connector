<?php

namespace Http\Client\Plugin;

@trigger_error('The '.__NAMESPACE__.'\HeaderDefaultsPlugin class is deprecated since version 1.1 and will be removed in 2.0. Use Http\Client\Common\Plugin\HeaderDefaultsPlugin instead.', E_USER_DEPRECATED);

use Psr\Http\Message\RequestInterface;

/**
 * Set default values for the request headers.
 * If a given header already exists the value wont be replaced and the request wont be changed.
 *
 * @author Soufiane Ghzal <sghzal@gmail.com>
 *
 * @deprecated since since version 1.1, and will be removed in 2.0. Use {@link \Http\Client\Common\Plugin\HeaderDefaultsPlugin} instead.
 */
class HeaderDefaultsPlugin implements Plugin
{
    private $headers = [];

    /**
     * @param array $headers headers to set to the request
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        foreach ($this->headers as $header => $headerValue) {
            if (!$request->hasHeader($header)) {
                $request = $request->withHeader($header, $headerValue);
            }
        }

        return $next($request);
    }
}
