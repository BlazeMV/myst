<?php

namespace Blaze\Myst;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Api\Request\BaseRequest;
use Blaze\Myst\Exceptions\ConfigurationException;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Traits\AvailableMethods;
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
		if (isset($this->config[$key])) return $this->config[$key];
		
		throw new ConfigurationException("Unknown config key $key");
	}
	
	public function handleUpdate(callable $pre_function = null)
	{
		$update = $this->getWebhookUpdate();
		
		if (is_callable($pre_function)){
			$pre_function($update);
		}
		
		return $update->processUpdate();
	}
	
	public function getWebhookUpdate()
	{
		$body = json_decode(file_get_contents('php://input'), true);
		return new Update($body, $this);
	}
	
	public function sendRequest(BaseRequest $request)
	{
		$request->setBot($this);
		return $request->send();
	}
}