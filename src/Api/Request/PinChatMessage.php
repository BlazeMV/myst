<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\Raw;

class PinChatMessage extends BaseRequest
{
    protected function responseObject()
    {
        return Raw::class;
    }
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }

    public function messageId($message_id)
    {
        $this->params['message_id'] = $message_id;
        return $this;
    }

    public function noNotify()
    {
        $this->params['disable_notification'] = true;
        return $this;
    }
}