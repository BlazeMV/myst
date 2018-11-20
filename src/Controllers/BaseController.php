<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Requests\AnswerCallbackQuery;
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
    
    /**
     * @var string $name Name of this controller
     */
    protected $name;
    
    /**
     * @var string $description Description of this controller
     */
    protected $description = "";
    
    /**
     * @var array $engages_in types of conversations this controller will respond to
     */
    protected $engages_in = [
        'private'       => true,
        'group'         => true,
        'supergroup'    => true,
        'channel'       => true
    ];
    
    /**
     * @param Bot $bot
     * @param Update $update
     * @param array $arguments
     * @return mixed
     * @throws \Blaze\Myst\Exceptions\MystException
     */
    public function make(Bot $bot, Update $update, array $arguments)
    {
        $this->setup($bot, $update);
        
        return $this->handle($arguments);
    }
    
    /**
     * @param Bot $bot
     * @param Update $update
     */
    protected function setup(Bot $bot, Update $update)
    {
        $this->bot = $bot;
        $this->update = $update;
    }
    
    /**
     * @param $arguments
     * @return mixed
     * @throws \Blaze\Myst\Exceptions\MystException
     */
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
     * @param BaseRequest $request
     * @return Response
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     * @throws \Blaze\Myst\Exceptions\HttpException
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    public function replyWith(BaseRequest $request)
    {
        $request->setBot($this->getBot());
        
        if ($request instanceof SendMessage)
            $request->to($this->getUpdate()->getChat()->getId())->replyTo($this->getUpdate()->getMessage()->getId());
            
        elseif ($request instanceof AnswerCallbackQuery)
            $request->to($this->getUpdate()->getCallbackQuery()->getId());
        
        
        return $request->send();
    }
}