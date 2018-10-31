<?php

namespace Blaze\Myst;

use Blaze\Myst\Exceptions\ConfigurationException;

class BotsManager {
	
	/**
	 * An array bot config array
	 *
	 * @var array $config
	*/
	protected $config = [];
	
	/**
	 * An array Bot objects
	 *
	 * @var array $config
	 */
	protected $bots;
	
	/**
	 * BotsManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	/**
	 * Get a config value from the Myst config.
	 *
	 * @param  string $key
	 * @return mixed
	 * @throws ConfigurationException
	 */
	public function getConfigValue($key)
	{
		if (isset($this->config[$key])) return $this->config[$key];
		
		throw new ConfigurationException("key $key not found in Myst config");
	}
	
	/**
	 * Get the default bot name.
	 *
	 * @return string
	 * @throws ConfigurationException
	 */
	public function getDefaultBotName()
	{
		return $this->getConfigValue('default');
	}
	
	/**
	 * Get the configuration for a bot.
	 *
	 * @param string|null $name
	 *
	 * @throws \InvalidArgumentException
	 * @throws ConfigurationException
	 *
	 * @return array
	 */
	public function getBotConfig($name = null)
	{
		$name = $name ?: $this->getDefaultBotName();
		
		$bots = $this->getConfigValue('bots');
		if (!isset($bots[$name])) throw new ConfigurationException("$name bot not configured in the config file.");
		
		$config = $bots[$name];
		$config['name'] = $name;
		
		return $config;
	}
	
	/**
	 * Make the bot instance.
	 *
	 * @param string $name
	 *
	 * @return Bot
	 * @throws ConfigurationException
	 */
	protected function makeBot($name)
	{
		$config = $this->getBotConfig($name);
		
		$bot = new Bot($config);
		
		return $bot;
	}
	
	/**
	 * Get a bot instance.
	 *
	 * @param string $name
	 *
	 * @return Bot
	 * @throws ConfigurationException
	 */
	public function getActiveBot($name = null)
	{
		$name = $name ?: $this->getDefaultBotName();
		
		if (!isset($this->bots[$name])) {
			$this->bots[$name] = $this->makeBot($name);
		}
		
		return $this->bots[$name];
	}
	
	/**
	 * Call default bot's methods
	 *
	 * @param string $method
	 * @param array $parameters
	 *
	 * @return mixed
	 * @throws ConfigurationException
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array([$this->getActiveBot(), $method], $parameters);
	}
}