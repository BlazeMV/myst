<?php

namespace Blaze\Myst\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Blaze\Myst\Api\Response;
use GuzzleHttp\Handler\CurlMultiHandler;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise;

/**
 * Used to make requests to APIs and return responses.
 *
 * Class HttpService
 * @package Blaze\Myst\Services
 */
class HttpService
{
	/**
	 * @var Client $client
	 */
	private $client;
	
	/**
	 * Headers to be set on every request
	 *
	 * @var array $headers
	 */
	private $headers = [
		'Accept' => 'application/json',
		'Connection' => 'keep-alive',
		'Accept-Language' => 'en-US,en;q=0.9'
	];
	
	/**
	 * HttpService constructor.
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}
    
    /**
     * @param string $url
     * @param array $data
     * @param bool $async
     * @param callable|null $async_function
     * @return Response
     */
	public function post($url, array $data, $async = false, callable $async_function = null)
	{
		return $this->makeRequest('POST', $url, $data, $async, $async_function);
	}
    
    /**
     * @param string $method
     * @param string $url
     * @param array|null $body
     * @param bool $async
     * @param callable|null $async_function
     * @return Response
     */
	private function makeRequest($method, $url, array $body = null, $async = false, callable $async_function = null)
	{
	    $curlMultiHandle = new CurlMultiHandler();
	    $options = [
            'headers'       => $this->headers,
            'form_params'   => $body,
            'synchronous'   => !$async,
            'handler'       => $curlMultiHandle
        ];
	    
        $promise = $this->client->requestAsync($method, $url, $options);
        $response = null;
        $exception = null;
        $code = -1;
        
        if ($async) {
            while (!Promise\is_settled($promise)) {
                $curlMultiHandle->tick();
            }
            
            $promise->then(function (ResponseInterface $response) use ($async_function, $promise, $options) {
                if (is_callable($async_function)){
                    $code = $response->getStatusCode();
    
                    $response = new Response($code, $options, $response, $promise, null);
                    $async_function($response);
                }
            });
            $code = 0;
        } else {
            try {
                $response = $promise->wait();
                $code = $response->getStatusCode();
            } catch (\Throwable $throwable) {
                $exception = $throwable;
                if ($throwable instanceof RequestException && $throwable->hasResponse()) {
                    $response = $throwable->getResponse();
                    $code = $response->getStatusCode();
                }
            }
        }
        
        return new Response($code, $options, $response, $promise, $exception);
	}
	
	/**
	 * add headers to request
	 * @param array $headers
	 * @return array
	 */
	public function addHeaders(array $headers)
	{
		foreach ($headers as $key => $header) {
			$this->headers[$key] = $header;
		}
		return $this->headers;
	}
}