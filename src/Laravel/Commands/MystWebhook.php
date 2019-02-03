<?php

namespace Blaze\Myst\Laravel\Commands;

use Blaze\Myst\Api\Requests\SetWebhook;
use Blaze\Myst\BotsManager;
use Illuminate\Console\Command;

class MystWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:webhook {route : The route name which will be used at webhook url}
                                         {bot? : Name of the bot from the myst config file}
                                         {--cert= : url to certificate file}
                                         {--conn= : Maximum allowed number of simultaneous connections to the webhook}
                                         {--t|type=* : Types of updates the bot will receive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set bot Webhook to the given route';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     * @throws \Blaze\Myst\Exceptions\StackException
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    public function handle()
    {
        $route = $this->argument('route');
        try {
            $url = url(route($route));
        } catch (\InvalidArgumentException $exception) {
            $this->error('Invalid route specified.');
            return false;
        }
        
        $manager = new BotsManager(config('myst'));
        if ($this->hasArgument('bot')) {
            $bot = $manager->getActiveBot($this->argument('bot'));
        } else {
            $bot = $manager->getActiveBot();
        }
        
        $request = SetWebhook::make()->url($url);
        if ($this->hasOption('cert')) {
            $request->certificate($this->option('cert'));
        }
        if ($this->hasOption('conn')) {
            $request->maxConnections($this->option('conn'));
        }
        if ($this->hasOption('type')) {
            $request->allowedUpdates($this->option('type'));
        }
        
        $response = $bot->sendRequest($request);
        if ($response->isOk()) {
            $this->info("Webhook was set.");
        } else {
            $this->error($response->getErrorMessage());
        }
        
        return true;
    }
    
    public function hasArgument($name)
    {
        return parent::hasArgument($name) && parent::argument($name) !== null;
    }
    
    public function hasOption($name)
    {
        return parent::hasOption($name) && parent::option($name) !== null;
    }
}
