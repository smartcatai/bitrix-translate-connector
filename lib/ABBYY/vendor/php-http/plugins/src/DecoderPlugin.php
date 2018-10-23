<?php

namespace Http\Client\Plugin;

@trigger_error('The '.__NAMESPACE__.'\DecoderPlugin class is deprecated since version 1.1 and will be removed in 2.0. Use Http\Client\Common\Plugin\DecoderPlugin instead.', E_USER_DEPRECATED);

use Http\Message\Encoding\DechunkStream;
use Http\Message\Encoding\DecompressStream;
use Http\Message\Encoding\GzipDecodeStream;
use Http\Message\Encoding\InflateStream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Allow to decode response body with a chunk, deflate, compress or gzip encoding.
 *
 * If zlib is not installed, only chunked encoding can be handled.
 *
 * If Content-Encoding is not disabled, the plugin will add an Accept-Encoding header for the encoding methods it supports.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 *
 * @deprecated since since version 1.1, and will be removed in 2.0. Use {@link \Http\Client\Common\Plugin\DecoderPlugin} instead.
 */
class DecoderPlugin implements Plugin
{
    /**
     * @var bool Whether this plugin decode stream with value in the Content-Encoding header (default to true).
     *
     * If set to false only the Transfer-Encoding header will be used.
     */
    private $useContentEncoding;

    /**
     * @param array $config {
     *
     *    @var bool $use_content_encoding Whether this plugin should look at the Content-Encoding header first or only at the Transfer-Encoding (defaults to true).
     * }
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'use_content_encoding' => true,
        ]);
        $resolver->setAllowedTypes('use_content_encoding', 'bool');
        $options = $resolver->resolve($config);

        $this->useContentEncoding = $options['use_content_encoding'];
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $encodings = extension_loaded('zlib') ? ['gzip', 'deflate', 'compress'] : ['identity'];

        if ($this->useContentEncoding) {
            $request = $request->withHeader('Accept-Encoding', $encodings);
        }
        $encodings[] = 'chunked';
        $request = $request->withHeader('TE', $encodings);

        return $next($request)->then(function (ResponseInterface $response) {
            return $this->decodeResponse($response);
        });
    }

    /**
     * Decode a response body given its Transfer-Encoding or Content-Encoding value.
     *
     * @param ResponseInterface $response Response to decode
     *
     * @return ResponseInterface New response decoded
     */
    private function decodeResponse(ResponseInterface $response)
    {
        $response = $this->decodeOnEncodingHeader('Transfer-Encoding', $response);

        if ($this->useContentEncoding) {
            $response = $this->decodeOnEncodingHeader('Content-Encoding', $response);
        }

        return $response;
    }

    /**
     * Decode a response on a specific header (content encoding or transfer encoding mainly).
     *
     * @param string            $headerName Name of the header
     * @param ResponseInterface $response   Response
     *
     * @return ResponseInterface A new instance of the response decoded
     */
    private function decodeOnEncodingHeader($headerName, ResponseInterface $response)
    {
        if ($response->hasHeader($headerName)) {
            $encodings = $response->getHeader($headerName);
            $newEncodings = [];

            while ($encoding = array_pop($encodings)) {
                $stream = $this->decorateStream($encoding, $response->getBody());

                if (false === $stream) {
                    array_unshift($newEncodings, $encoding);

                    continue;
                }

                $response = $response->withBody($stream);
            }

            $response = $response->withHeader($headerName, $newEncodings);
        }

        return $response;
    }

    /**
     * Decorate a stream given an encoding.
     *
     * @param string          $encoding
     * @param StreamInterface $stream
     *
     * @return StreamInterface|false A new stream interface or false if encoding is not supported
     */
    private function decorateStream($encoding, StreamInterface $stream)
    {
        if (strtolower($encoding) == 'chunked') {
            return new DechunkStream($stream);
        }

        if (strtolower($encoding) == 'compress') {
            return new DecompressStream($stream);
        }

        if (strtolower($encoding) == 'deflate') {
            return new InflateStream($stream);
        }

        if (strtolower($encoding) == 'gzip') {
            return new GzipDecodeStream($stream);
        }

        return false;
    }
}
