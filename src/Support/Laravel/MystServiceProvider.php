<?php

namespace Blaze\Myst\Support\Laravel;

use Blaze\Myst\BotsManager;
use Blaze\Myst\Support\Laravel\Commands\MystCallbackQuery;
use Blaze\Myst\Support\Laravel\Commands\MystCommand;
use Blaze\Myst\Support\Laravel\Commands\MystConversation;
use Illuminate\Support\ServiceProvider;

class MystServiceProvider extends ServiceProvider
{
	/**
	 * Boot the service provider.
	 */
	public function boot()
	{
		$this->makeConfig();
		
		$this->registerCommands();
	}
	
	protected function makeConfig()
	{
		$config_path = dirname(__DIR__) . '/config.php';
		$this->publishes([$config_path => config_path('myst.php')], 'Myst');
	}
	
	public function registerCommands(){
		if ($this->app->runningInConsole()) {
			$this->commands([
			    MystCommand::class,
			    MystCallbackQuery::class,
			    MystConversation::class,
            ]);
		}
	}
	
	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->app->singleton('Blaze\Myst\Bot', function () {
			$config = config('myst');
			$manager = new BotsManager($config);
			return $manager->getActiveBot();
		});
	}
}