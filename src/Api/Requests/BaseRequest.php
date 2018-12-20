<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Response;
use Blaze\Myst\Bot;
use Blaze\Myst\Exceptions\RequestException;
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
    protected $async;
    
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
     * @return string
     */
    abstract protected function responseObject() : string ;
    
    /**
     * get the class name of the response object to return
     * @return bool
     */
    protected function multipleResponseObjects() : bool
    {
        return false;
    }
    
    /**
     * @return string
     */
    public function getMethod(): string
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
    
    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->hasParam($key) ? $this->params[$key] : null;
    }
    
    /**
     * @param $key
     * @return mixed
     */
    public function hasParam($key)
    {
        return array_has($this->params, $key);
    }
    
    /**
     * @param array $params
     * @return BaseRequest
     */
    public function setParams(array $params): BaseRequest
    {
        $this->params = $params;
        return $this;
    }
    
    /**
     * @param $key
     * @param $value
     * @return BaseRequest
     */
    public function addParam($key, $value): BaseRequest
    {
        $this->params[$key] = $value;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isAsync(): bool
    {
        if ($this->async !== null) {
            return $this->async;
        }
        return $this->getBot()->getConfig('async');
    }
    
    /**
     * @param bool $async
     * @return BaseRequest
     */
    public function async(bool $async = true): BaseRequest
    {
        $this->async = $async;
        return $this;
    }
    
    /**
     * @return HttpService
     */
    public function getHttpService(): HttpService
    {
        return $this->http_service;
    }
    
    /**
     * @param HttpService $http_service
     * @return BaseRequest
     */
    public function setHttpService(HttpService $http_service): BaseRequest
    {
        $this->http_service = $http_service;
        return $this;
    }
    
    /**
     * @return Bot
     */
    public function getBot(): Bot
    {
        return $this->bot;
    }
    
    /**
     * @param Bot $bot
     * @return BaseRequest
     */
    public function setBot(Bot $bot): BaseRequest
    {
        $this->bot = $bot;
        return $this;
    }
    
    /**
     * @param callable|null $async_function
     * @return Response
     * @throws RequestException
     */
    public function send(callable $async_function = null)
    {
        if ($this->bot === null) {
            throw new RequestException("setBot() method must be called before calling send() method");
        }
        
        $this->prepareRequest();
        $response = $this->http_service->post(
            ConfigService::getTelegramApiUrl() . $this->getBot()->getConfig('token') . '/',
            $this->getParams(),
            $this->isAsync(),
            $async_function
        );
        
        if ($response->isOk()) {
            $response->setResponseObject($this->responseObject(), $this->multipleResponseObjects());
        }
        
        return $response;
    }
    
    /**
     * @return $this
     */
    private function prepareRequest()
    {
        $this->addParam('method', $this->getMethod());
        
        if ($this->hasParam('reply_markup')) {
            ($this->addParam('reply_markup', $this->getParam('reply_markup')->serialize()));
        }
        
        return $this;
    }
}
