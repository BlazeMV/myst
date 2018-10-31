<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Api\Requests\BaseRequest;
use Blaze\Myst\Api\Requests\SendMessage;

trait ReplyWith
{
    public function replyWithMessage()
    {
        return SendMessage::make()->to($this->getUpdate()->getChat()->getId())->replyTo($this->getUpdate()->getMessage()->getId())->setBot($this->getBot());
    }
    
    public function replyWith(BaseRequest $request)
    {
        if ($request instanceof SendMessage)
        return $request->setBot($this->getBot())->to($this->getUpdate()->getChat()->getId())->replyTo($this->getUpdate()->getMessage()->getId())->send();
    }
}