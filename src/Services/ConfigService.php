<?php

namespace Blaze\Myst\Services;

use Blaze\Myst\Exceptions\ConfigurationException;

class ConfigService
{
	/**
	 * returns telegram bot api url
	 *
	 * @return string
	 */
	public static function getTelegramApiUrl()
	{
		return 'https://api.telegram.org/bot';
	}
	
	/**
	 * validates a bot config array
	 *
	 * @param array $config
	 * @throws ConfigurationException
	 */
	public static function validateBotConfig(array $config)
	{
		$required = [
			'username',
			'token',
			'async',
			'process_edited_messages',
			'commands_param_seperator',
			'cbq_param_seperator',
			'unknown_command_reply_help',
			'engages_in',
			'commands',
			'callback_queries',
			'messages'
		];
		$string = [
			'username',
			'token',
			'commands_param_seperator',
			'cbq_param_seperator'
		];
		$boolean = [
			'async',
			'process_edited_messages',
			'unknown_command_reply_help'
		];
		$array = [
			'engages_in',
			'commands',
			'callback_queries',
			'messages'
		];
		$regex = [
			'token' => '/^[0-9]{9}:[a-zA-Z0-9-*_*]{35}$/'
		];
		
		foreach ($required as $item) {
			if (!array_has($config, $item)) {
				throw new ConfigurationException("Required config value $item is missing from the config array.");
			}
		}
		
		foreach ($string as $item) {
			if (!isset($config[$item])) continue;
			
			if (!is_string($config[$item]) || strlen($config[$item]) < 1) {
				throw new ConfigurationException("$item is expected to be a string.");
			}
		}
		
		foreach ($boolean as $item) {
			if (!isset($config[$item])) continue;
			
			if (!is_bool($config[$item])) {
				throw new ConfigurationException("$item is expected to be a boolean true or false.");
			}
		}
		
		foreach ($array as $item) {
			if (!isset($config[$item])) continue;
			
			if (!is_array($config[$item])) {
				throw new ConfigurationException("$item is expected to be an array.");
			}
		}
		
		foreach ($regex as $item => $pattern) {
			if (!isset($config[$item])) continue;
			
			if (preg_match($pattern, $config[$item]) !== 1) {
				throw new ConfigurationException("$item does not match the required pattern");
			}
		}
	}
}