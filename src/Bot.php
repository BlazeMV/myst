<?php

namespace Blaze\Myst;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Api\Requests\BaseRequest;
use Blaze\Myst\Exceptions\ConfigurationException;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Traits\RequestHandler;
use Blaze\Myst\Traits\StacksHandler;
use Blaze\Myst\Traits\UpdateHandler;

class Bot
{
	use StacksHandler;
	use UpdateHandler;
	use RequestHandler;
	
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
}
