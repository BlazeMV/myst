<?php

namespace Blaze\Myst\Controllers;

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
    
    protected $position = 0;
    
    protected $alone = true;
    
    protected $engage = [
        'private',
        'group',
        'supergroup',
        'channel'
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
    
    abstract public function handle($arguments);
    
    /**
     * @return Bot
     */
    public function getBot()
    {
        return $this->bot;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    /**
     * @return Update
     */
    public function getUpdate()
    {
        return $this->update;
    }
    
    /**
     * @return string|integer
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * @return bool
     */
    public function onlyWhenAlone(): bool
    {
        return $this->alone;
    }
    
    /**
     * @return bool
     */
    public function engagesIn($chat_type): bool
    {
        return array_has($this->engage, $chat_type);
    }
    
    /**
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->case_sensitive;
    }
}