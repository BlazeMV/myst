<?php

namespace Blaze\Myst\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Blaze\Myst\Exceptions\HttpException;
use Blaze\Myst\Api\Response;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\RequestOptions;

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
    
    /** @var PromiseInterface[] Holds promises. */
    private static $promises = [];
	
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
     * @return Response
     * @throws HttpException
     */
	public function post($url, array $data, $async = false)
	{
		return $this->makeRequest('POST', $url, $data, $async);
	}
    
    /**
     * @param string $method
     * @param string $url
     * @param array|null $body
     * @param bool $async
     * @return Response
     */
	private function makeRequest($method, $url, array $body = null, $async = false)
	{
	    $options = [
            'headers'       => $this->headers,
            'form_params'   => $body,
            'synchronous'   => $async,
        ];
        
	    Log::info("Sending request");
        $promise = $this->client->requestAsync($method, $url, $options);
        $response = null;
        $exception = null;
        $code = -1;
        
        if ($async) {
            $promise->then(function (ResponseInterface $response) {
                Log::info("Response received");
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
        
        Log::alert("Continuing with execution");
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