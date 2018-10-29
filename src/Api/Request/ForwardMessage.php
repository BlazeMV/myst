<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\Message;

class ForwardMessage extends BaseRequest
{
    protected function responseObject()
    {
        return Message::class;
    }
    
    
    public function from($chat_id)
    {
        $this->params['from_chat_id'] = $chat_id;
        return $this;
    }

    public function to($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }

    public function id($message_id)
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