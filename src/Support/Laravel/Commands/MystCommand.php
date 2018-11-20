<?php

namespace Blaze\Myst\Support\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:command {name}';

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
        $file_path = app_path() . "/Telegram/Commands/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('command', $name, $file_path);
        $this->info("$name Command created at app/Telegram/Commands");
    }
}