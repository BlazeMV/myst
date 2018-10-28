<?php

namespace Blaze\Myst\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Blaze\Myst\Exceptions\HttpException;
use Blaze\Myst\Api\Response;

/**
 * Used to make requests to APIs and return responses.
 *
 * Class HttpService
 * @package Msd\Citadel\Core\Services
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
	 * @return Response
	 * @throws HttpException
	 */
	public function post($url, array $data)
	{
		return $this->makeRequest('POST', $url, $data);
	}
	
	/**
	 * @param string $method
	 * @param string $url
	 * @param array|null $body
	 * @return Response
	 * @throws HttpException
	 */
	private function makeRequest($method, $url, array $body = null)
	{
		try {
			$response = $this->client->request($method, $url, [
				'json' => $body,
				'headers' => $this->headers
			]);
			return new Response($response);
			
		} catch (GuzzleException $exception) {
			if ($exception instanceof ClientException || $exception instanceof RequestException || $exception instanceof ServerException) {
				return new Response($exception->getResponse(), $exception->getRequest(), $exception->getTrace());
			} else {
				throw new HttpException("Unknown Exception thrown from GuzzleHttp\Client.", 0, $exception);
			}
		}
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