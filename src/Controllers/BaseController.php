<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Requests\BaseRequest;
use Blaze\Myst\Api\Requests\SendMessage;
use Blaze\Myst\Api\Response;
use Blaze\Myst\Bot;
use Blaze\Myst\Api\Objects\Update;

abstract class BaseController
{
    /**
     * @var $bot Bot
     */
    protected $bot;
    
    /**
     * @var $update Update
     */
    protected $update;
    
    protected $name;
    
    protected $aliases = [];
    
    protected $description = "";
    
    protected $position = '*';
    
    protected $only_command = true;
    
    protected $engages_in = [
        'private'       => true,
        'group'         => true,
        'supergroup'    => true,
        'channel'       => true
    ];
    
    protected $case_sensitive = false;
    
    public function make(Bot $bot, Update $update, array $arguments)
    {
        $this->setup($bot, $update);
        
        return $this->handle($arguments);
    }
    
    protected function setup(Bot $bot, Update $update)
    {
        $this->bot = $bot;
        $this->update = $update;
    }
    
    abstract protected function handle($arguments);
    
    /**
     * @return Bot
     */
    public function getBot(): Bot
    {
        return $this->bot;
    }
    
    /**
     * @param Bot $bot
     * @return BaseController
     */
    public function setBot(Bot $bot): BaseController
    {
        $this->bot = $bot;
        return $this;
    }
    
    /**
     * @return Update
     */
    public function getUpdate(): Update
    {
        return $this->update;
    }
    
    /**
     * @param Update $update
     * @return BaseController
     */
    public function setUpdate(Update $update): BaseController
    {
        $this->update = $update;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param mixed $name
     * @return BaseController
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }
    
    /**
     * @param array $aliases
     * @return BaseController
     */
    public function setAliases(array $aliases): BaseController
    {
        $this->aliases = $aliases;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     * @return BaseController
     */
    public function setDescription(string $description): BaseController
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }
    
    /**
     * @param string $position
     * @return BaseController
     */
    public function setPosition(string $position): BaseController
    {
        $this->position = $position;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isOnlyCommand(): bool
    {
        return $this->only_command;
    }
    
    /**
     * @param bool $only_command
     * @return BaseController
     */
    public function setOnlyCommand(bool $only_command): BaseController
    {
        $this->only_command = $only_command;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getEngagesIn(): array
    {
        return $this->engages_in;
    }
    
    /**
     * @param array $engages_in
     * @return BaseController
     */
    public function setEngagesIn(array $engages_in): BaseController
    {
        $this->engages_in = $engages_in;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->case_sensitive;
    }
    
    /**
     * @param bool $case_sensitive
     * @return BaseController
     */
    public function setCaseSensitive(bool $case_sensitive): BaseController
    {
        $this->case_sensitive = $case_sensitive;
        return $this;
    }
    
    /**
     * @param BaseRequest $request
     * @return Response
     */
    public function replyWith(BaseRequest $request)
    {
        if ($request instanceof SendMessage)
            return $request->setBot($this->getBot())->to($this->getUpdate()->getChat()->getId())->replyTo($this->getUpdate()->getMessage()->getId())->send();
    }
}