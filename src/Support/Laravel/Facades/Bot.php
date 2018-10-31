<?php

namespace Blaze\Myst\Support\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Blaze\Myst\Api\Objects\Update handleUpdate(callable $pre_function = null)
 * @method static \Blaze\Myst\Api\Objects\Update getWebhookUpdate()
*/
class Bot extends Facade
{
	/**
	 * Get the registered name of the component.
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return \Blaze\Myst\Bot::class;
	}
}