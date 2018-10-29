<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Bot;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Services\HttpService;
use GuzzleHttp\Client;

abstract class BaseRequest
{
	/**
	 * telegram bot api method https://core.telegram.org/bots/api#available-methods
	 * @var string $method
	*/
	protected $method;
	
	/**
	 * Data to send with the request https://core.telegram.org/bots/api#available-methods
	 * @var array $params
	*/
	protected $params = [];
	
	/**
	 * whether or not to make an async request to api
	 * @var bool $async
	*/
	protected $async = [];
	
	/**
	 * HttpService that will be used to make the request
	 * @var HttpService $http_service
	*/
	protected $http_service;
	
	/**
	 * @var Bot $bot
	*/
	protected $bot;
	
	public static function make()
	{
		$me = new static;
		$me->method = camel_case(class_basename(static::class));
		$me->http_service = new HttpService(new Client());
		return $me;
	}
    
    /**
     * get the class name of the response object to return
     *
     */
    protected abstract function responseObject();
    
    protected function multipleResponseObjects()
    {
        return false;
    }
	
	/**
	 * @return mixed
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}
	
	public function setParams(array $params)
	{
		$this->params = $params;
	}
	
	public function async()
	{
		$this->async = true;
		
		return $this;
	}
	
	public function getAsync()
	{
		return $this->async;
	}
	
	public function setBot(Bot $bot)
	{
		$this->bot = $bot;
	}
	
	public function send()
	{
		$this->prepareRequest();
		$response = $this->http_service->post(ConfigService::getTelegramApiUrl() . $this->bot->getConfig('token') . '/', $this->params);
		
		return $response->castToObject($this->responseObject(), $this->multipleResponseObjects());
	}
	
	private function prepareRequest()
	{
		$this->params['method'] = $this->method;
		
		return $this;
	}
}