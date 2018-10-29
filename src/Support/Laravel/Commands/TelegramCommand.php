<?php

namespace Blaze\Blazing\Laravel\Commands;

use function GuzzleHttp\Psr7\str;
use Illuminate\Console\Command;

class TelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:blazing {what} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Bot Command or CallbackQuery at app/Telegram';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('what')){
            case 'command':
            case 'cmd':
                $this->makeCommand($this->argument('name'));
                break;
            case 'callbackquery':
            case 'cbq':
                $this->makeCallbackQuery($this->argument('name'));
                break;
            case 'conversation':
                $this->makeConversation($this->argument('name'));
                break;
            default:
                $this->error("Unsupported command!");
        }
    }

    protected function getStub($name)
    {
        return \File::get(__DIR__."/stubs/$name.stub");
    }

    protected function fillStub($stub, $name)
    {
        return str_ireplace(['DummyName', 'DummyClassName'], [strtolower($name), $name], $stub);
    }

    protected function makeFile($path, $name, $content)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        \File::put($path . $name . '.php', $content);
    }

    protected function makeCommand($name)
    {
        $file_path = app_path() . "/Telegram/Commands/";
        $name = studly_case($name);

        $this->makeFile($file_path, $name . 'Command', $this->fillStub($this->getStub('command'), $name));
        $this->info("New Bot Command created!");
    }

    protected function makeCallbackQuery($name)
    {
        $file_path = app_path() . "/Telegram/CallbackQueries/";
        $name = studly_case($name);

        $this->makeFile($file_path, $name . 'CallbackQuery', $this->fillStub($this->getStub('callbackquery'), $name));
        $this->info("New Bot Callback Query created.");
    }

    protected function makeConversation($name)
    {
        $file_path = app_path() . "/Telegram/Conversations/";
        $name = studly_case($name);

        $this->makeFile($file_path, $name . 'Conversation', $this->fillStub($this->getStub('conversation'), $name));
        $this->info("New Bot Conversation created.");
    }
}