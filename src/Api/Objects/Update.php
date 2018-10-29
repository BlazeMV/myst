<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Bot;

class Update extends ApiObject
{
    /**
     * @var Bot $bot
    */
    protected $bot;
    public function __construct($data, Bot $bot)
    {
        $this->bot = $bot;
        
        parent::__construct($data);
    }
    
    public function processUpdate()
    {
        return $this;
    }
}