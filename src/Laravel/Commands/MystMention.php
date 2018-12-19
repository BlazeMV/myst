<?php

namespace Blaze\Myst\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystMention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:mention {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Myst Mention Controller at App\Telegram\Mentions namespace';
    
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
        $file_path = app_path() . "/Telegram/Mentions/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('mention', $name, $file_path);
        $this->info("$name mention created at app/Telegram/Mentions");
    }
}
