<?php

namespace Blaze\Myst\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:text {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Myst Text Controller at App\Telegram\Texts namespace';
    
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
        $file_path = app_path() . "/Telegram/Texts/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('text', $name, $file_path);
        $this->info("$name text created at app/Telegram/Texts");
    }
}