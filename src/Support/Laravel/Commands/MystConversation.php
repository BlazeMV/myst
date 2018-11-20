<?php

namespace Blaze\Myst\Support\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystConversation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:conversation {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Myst Conversation Controller at App\Telegram\Conversations namespace';
    
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
        $file_path = app_path() . "/Telegram/Conversations/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('conversation', $name, $file_path);
        $this->info("$name conversation created at app/Telegram/Conversations");
    }
}