<?php

namespace Blaze\Myst;

use Blaze\Myst\Exceptions\ConfigurationException;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Traits\AvailableMethods;
use Blaze\Myst\Traits\StacksHandler;

class Bot
{
	use StacksHandler;
	
	use AvailableMethods;
	
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
}