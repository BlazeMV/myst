<?php

namespace Blaze\Myst\Laravel\Commands;

use Blaze\Myst\Services\StubService;
use Illuminate\Console\Command;

class MystHashtag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myst:hashtag {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Myst Hashtag Controller at App\Telegram\Hashtags namespace';
    
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
        $file_path = app_path() . "/Telegram/Hashtags/";
        $name = studly_case($this->argument('name'));
    
        $service->makeStub('hashtag', $name, $file_path);
        $this->info("$name hashtag created at app/Telegram/Hashtags");
    }
}
