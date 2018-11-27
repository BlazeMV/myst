<?php

namespace Blaze\Myst\Api;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents a Response from an Api request
 * Class Response
 * @package Blaze\Myst\Api\Response
 */
class Response
{
    /**
     * @var int $statusCode
     */
    protected $statusCode;
    
    
	/**
	 * @var ResponseInterface $response
	 */
	protected $response;
	
	
	/**
	 * @var PromiseInterface $promise
	 */
	protected $promise;
	
	
	/**
	 * @var array $request
	 */
	protected $request;
    
	
    /**
     * @var \Throwable $exception
     */
    protected $exception;

    
    /**
     * @var mixed
     */
    protected $object;
    
    private static $scalar_types = ['int', 'integer', 'bool', 'boolean', 'float', 'double', 'real', 'string', 'array', 'object', 'unset'];
    
    /**
     * Response constructor.
     * @param int $statusCode
     * @param array $request
     * @param ResponseInterface|null $response
     * @param PromiseInterface|null $promise
     * @param \Throwable|null $exception
     */
    public function __construct(int $statusCode, array $request, ResponseInterface $response = null, PromiseInterface $promise = null, \Throwable $exception = null)
    {
        $this->statusCode = $statusCode;
        $this->response = $response;
        $this->promise = $promise;
        $this->request = $request;
        $this->exception = $exception;
    }
	
	/**
	 * whether or not this response is an ok response (http code 200 - 299)
	 * @return bool
	 */
	public function isOk() : bool
	{
		if ($this->getStatusCode() >=200 && $this->getStatusCode() < 300 && $this->getResponse() && $this->getResponseBody()['ok']) return true;
        
        return false;
	}
    
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * @return PromiseInterface
     */
    public function getPromise(): PromiseInterface
    {
        return $this->promise;
    }
    
    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }
    
    /**
     * @return \Throwable|null
     */
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * @return array|null
     */
    public function getResponseBody()
    {
        if ($this->getResponse() === null) return null;
        $this->getResponse()->getBody()->rewind();
        return json_decode($this->getResponse()->getBody()->getContents(), true);
    }
    
    /**
     * @return null|string
     */
    public function getErrorMessage()
    {
        if ($this->getStatusCode() === 0) return 'Async Request';
        
        if (isset($this->getResponseBody()['description'])) return $this->getResponseBody()['description'];
        
        if ($this->getResponse() !== null) return $this->getResponse()->getReasonPhrase();
        
        if ($this->getException()) return $this->getException()->getMessage();
        
        return null;
    }
    
    /**
     * @param $class
     * @param bool $multiple
     * @return $this
     */
    public function setResponseObject($class, bool $multiple = false)
    {
        if (in_array($class, static::$scalar_types)) {
            $this->object = $this->getResponseBody();
            settype($this->object, $class);
        }else {
            if ($multiple) {
                foreach ($this->getResponseBody()['result'] as $item) {
                    $this->object[] = new $class($item);
                }
        
            } else {
                $this->object = new $class($this->getResponseBody()['result']);
            }
        }
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getResponseObject()
    {
        return $this->object;
    }
}