<?php

namespace Blaze\Myst;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Api\Requests\BaseRequest;
use Blaze\Myst\Exceptions\ConfigurationException;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Traits\StacksHandler;

class Bot
{
	use StacksHandler;
	
	/**
	 * Bot configs
	 *
	 * @var array $config
	*/
	protected $config = [];
    
    /**
     * Bot constructor.
     *
     * @param array $config
     * @throws ConfigurationException
     * @throws Exceptions\StackException
     */
	public function __construct(array $config)
	{
		ConfigService::validateBotConfig($config);
		$this->config = $config;
		
		$this->populateStacks($config);
	}
	
	/**
	 * gets a value from the bot's config array
	 * 
	 * @param $key
	 * @return mixed
	 * @throws ConfigurationException
	 */
	public function getConfig($key)
	{
	    if (property_exists($this, $key)) return $this->$key;
	    
		if (array_has($this->config, $key)) return array_get($this->config, $key);
		
		throw new ConfigurationException("Unknown config key $key");
	}
    
    /**
     * @param bool $async
     * @return $this
     */
    public function async(bool $async = true)
    {
        $this->async = $async;
        return $this;
	}
    
    /**
     * @param callable|null $pre_function
     * @return Update
     * @throws Exceptions\MystException
     */
    public function handleUpdate(callable $pre_function = null)
	{
		$update = $this->getWebhookUpdate();
		
		if (is_callable($pre_function)){
			$pre_function($update);
		}
		
		return $update->processUpdate();
	}
    
    /**
     * @return Update
     * @throws Exceptions\MystException
     */
    public function getWebhookUpdate()
	{
		$body = json_decode(file_get_contents('php://input'), true);
		return (new Update($body))->setBot($this);
	}
    
    /**
     * @param BaseRequest $request
     * @return Api\Response
     * @throws ConfigurationException
     * @throws Exceptions\HttpException
     * @throws Exceptions\RequestException
     */
    public function sendRequest(BaseRequest $request)
	{
		$request->setBot($this);
		return $request->send();
	}
}