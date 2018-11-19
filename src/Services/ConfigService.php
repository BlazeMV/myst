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
    
    public static function getConversationCacheKey()
    {
        return 'myst_convo';
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
			'commands_param_separator',
			'cbq_param_separator',
			'unknown_command_reply_help',
			'engages_in',
			'engages_in.private',
			'engages_in.group',
			'engages_in.supergroup',
			'engages_in.channel',
			'commands',
			'callback_queries',
			'texts',
			'process',
			'process.commands',
			'process.callback_queries',
			'process.text',
			'process.hashtags',
			'process.mentions',
		];
		$string = [
			'username',
			'token',
			'commands_param_separator',
			'cbq_param_separator',
		];
		$boolean = [
			'async',
			'process_edited_messages',
			'unknown_command_reply_help',
            'engages_in.private',
            'engages_in.group',
            'engages_in.supergroup',
            'engages_in.channel',
            'process.commands',
            'process.callback_queries',
            'process.text',
            'process.hashtags',
            'process.mentions',
		];
		$array = [
			'engages_in',
			'commands',
			'callback_queries',
			'texts',
			'process',
		];
		$regex = [
			'token' => '/^[0-9]{9}:[a-zA-Z0-9-*_*]{35}$/',
		];
		
		foreach ($required as $item) {
			if (!array_has($config, $item)) {
				throw new ConfigurationException("Required config value $item is missing from the config array.");
			}
		}
		
		foreach ($string as $item) {
			if (!array_has($config, $item)) continue;
			
			if (!is_string(array_get($config, $item)) || strlen(array_get($config, $item)) < 1) {
				throw new ConfigurationException("$item is expected to be a string.");
			}
		}
		
		foreach ($boolean as $item) {
			if (!array_has($config, $item)) continue;
			
			if (!is_bool(array_get($config, $item))) {
				throw new ConfigurationException("$item is expected to be a boolean true or false.");
			}
		}
		
		foreach ($array as $item) {
			if (!array_has($config, $item)) continue;
			
			if (!is_array(array_get($config, $item))) {
				throw new ConfigurationException("$item is expected to be an array.");
			}
		}
		
		foreach ($regex as $item => $pattern) {
			if (!array_has($config, $item)) continue;
			
			if (preg_match($pattern, array_get($config, $item)) !== 1) {
				throw new ConfigurationException("$item does not match the required pattern");
			}
		}
	}
}