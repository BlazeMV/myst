<?php

namespace Blaze\Myst\Laravel;

use Blaze\Myst\BotsManager;
use Blaze\Myst\Services\ConfigService;
use Blaze\Myst\Laravel\Commands\MystCallbackQuery;
use Blaze\Myst\Laravel\Commands\MystCommand;
use Blaze\Myst\Laravel\Commands\MystConversation;
use Blaze\Myst\Laravel\Commands\MystHashtag;
use Blaze\Myst\Laravel\Commands\MystMention;
use Blaze\Myst\Laravel\Commands\MystText;
use Illuminate\Support\ServiceProvider;

class MystServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     * @throws \ReflectionException
     */
	public function boot()
	{
		$this->makeConfig();
		
		$this->registerCommands();
		
		if (ConfigService::shouldMaintainDatabase()) {
		    $this->loadMigrations();
        }
	}
	
	protected function makeConfig()
	{
		$config_path = dirname(__DIR__) . '/config.php';
		$this->publishes([$config_path => config_path('myst.php')], 'Myst');
	}
	
    protected function registerCommands(){
		if ($this->app->runningInConsole()) {
			$this->commands([
			    MystCommand::class,
			    MystCallbackQuery::class,
			    MystConversation::class,
			    MystHashtag::class,
			    MystMention::class,
			    MystText::class,
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
    
    /**
     * @throws \ReflectionException
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(ConfigService::getPackageAbsolutePath() . 'Laravel/Migrations');
    }
}