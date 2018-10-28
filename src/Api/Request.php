<?php

namespace Blaze\Myst\Api;

use Blaze\Myst\Services\HttpService;
use GuzzleHttp\Client;

class Request
{
	protected $method;
	
	protected $data;
	
	protected $http_service;
	
	/**
	 * Request constructor.
	 * @param $method
	 * @param $data
	 */
	public function __construct($method = null, $data = [])
	{
		$this->method = $method;
		$this->data = $data;
		$this->http_service = new HttpService(new Client());
	}
	
	
	/**
	 * @param mixed $method
	 * @return Request
	 */
	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}
	
	/**
	 * @param mixed $data
	 * @return Request
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	public function send()
	{
//		$this->http_service->post('https://')
	}
}