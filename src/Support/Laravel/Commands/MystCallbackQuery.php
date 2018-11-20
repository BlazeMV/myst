<?php

namespace Blaze\Myst\Support\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystCallbackQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:cbq {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Bot Command at app/Telegram namespace';
    
    /**
     * Execute the console command.
     *
     * @param StubService $service
     * @return mixed
     * @throws \Blaze\Myst\Exceptions\ControllerExistsException
     * @throws \Blaze\Myst\Exceptions\StubException
     * @throws \ReflectionException
     */
    public function handle(StubService $service)
    {
        $file_path = app_path() . "/Telegram/CallbackQueries/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('callbackquery', $name, $file_path);
        $this->info("$name Command created at app/Telegram/CallbackQueries");
    }
}