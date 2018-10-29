<?php

namespace Blaze\Myst\Support\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

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