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
     * @param Update $update
     * @return mixed
     */
    public function make(Update $update)
    {
        $this->setup($update);
        
        $this->handle();
            
        return $this;
    }
    
    /**
     * @param Update $update
     */
    protected function setup(Update $update)
    {
        $this->bot = $update->getBot();
        $this->update = $update;
    }
    
    /**
     * @return mixed
     */
    abstract protected function handle();
    
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
     * override specific values of $engages_in array
     *
     * @return array
     */
    public function engagesIn() : array
    {
        return [];
    }
    
    /**
     * @return array
     */
    public function getEngagesIn(): array
    {
        return array_merge($this->engages_in, $this->engagesIn());
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
     * @return string
     */
    public function prefix()
    {
        return '';
    }
    
    /**
     * @param BaseRequest $request
     * @param null $async_function
     * @return Response
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     * @throws \Blaze\Myst\Exceptions\RequestException
     */
    public function replyWith(BaseRequest $request, $async_function = null)
    {
        $request->setBot($this->getBot());
        
        if ($request instanceof SendMessage)
            $request->to($this->getUpdate()->getChat()->getId())->replyTo($this->getUpdate()->getMessage()->getId());
            
        elseif ($request instanceof AnswerCallbackQuery)
            $request->to($this->getUpdate()->getCallbackQuery()->getId());
        
        
        return $this->getBot()->sendRequest($request, $async_function);
    }
}