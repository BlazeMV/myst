<?php

namespace Blaze\Myst\Api;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Blaze\Myst\Exceptions\UnexpectedObjectTypeException;

/**
 * Represents a Response from an Api request
 * Class Response
 * @package Msd\Citadel
 */
class Response
{
	/**
	 * @var ResponseInterface $response
	 */
	protected $response;
	
	/**
	 * @var RequestInterface $request
	 */
	protected $request;
	
	/**
	 * @var int $statusCode
	 */
	protected $statusCode;
	
	/**
	 * @var mixed $response_body
	 */
	protected $response_body;
	
	/**
	 * @var mixed $request_body
	 */
	protected $request_body;
	
	/**
	 * @var string $message
	 */
	protected $message;
	
	/**
	 * @var array $trace
	 */
	protected $trace;
	
	/**
	 * Response constructor.
	 * @param ResponseInterface $response
	 * @param RequestInterface|null $request
	 * @param array|null $trace
	 */
	public function __construct(ResponseInterface $response, RequestInterface $request = null, array $trace = null)
	{
		$this->response = $response;
		$this->request = $request;
		$this->statusCode = $response->getStatusCode();
		$this->response_body = json_decode($response->getBody()->getContents(), true);
		$this->message = $response->getReasonPhrase();
		$this->trace = $trace;
		if ($request !== null) $this->request_body = json_decode($request->getBody()->getContents(), true);
	}
	
	/**
	 * convert this response to an Api object
	 * @param string $class
	 * @param boolean $multiple_objects
	 * @return Mixed
	 */
	public function castToObject($class, $multiple_objects = false)
	{
		/*if ($this->isOk()) {
			
			if (!is_array($this->getResponseBody()) && $class !== StringObject::class)
				throw new UnexpectedObjectTypeException("Expected an array, but received a string from api.");
			
			if ($multiple_objects){
				$objects = [];
				foreach ($this->getResponseBody() as $item) {
					$objects[] = new $class($item);
				}
				return new Collection($objects);
			} else {
				return new $class($this->getResponseBody());
			}
		} else {
			return new Error([
				'error_code' => $this->getStatusCode(),
				'errors' => $this->getResponseBody(),
				'response_body' => $this->getResponseBody(),
				'request_body' => $this->getRequestBody(),
				'response' => $this->getResponse(),
				'request' => $this->getRequest(),
				'message' =>$this->getMessage(),
				'trace' => $this->getTrace(),
			]);
		}*/
	}
	
	/**
	 * whether or not this response is an ok response (http code 200 - 299)
	 * @return bool
	 */
	public function isOk()
	{
		return ($this->statusCode >=200 && $this->statusCode < 300);
	}
	
	/**
	 * @return ResponseInterface
	 */
	public function getResponse(): ResponseInterface
	{
		return $this->response;
	}
	
	/**
	 * @return RequestInterface
	 */
	public function getRequest(): RequestInterface
	{
		return $this->request;
	}
	
	/**
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->statusCode;
	}
	
	/**
	 * @return mixed
	 */
	public function getResponseBody()
	{
		return $this->response_body;
	}
	
	/**
	 * @return mixed
	 */
	public function getRequestBody()
	{
		return $this->request_body;
	}
	
	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}
	
	/**
	 * @return array
	 */
	public function getTrace(): array
	{
		return $this->trace;
	}
}