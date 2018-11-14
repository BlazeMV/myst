<?php

namespace Blaze\Myst\Api\Requests;

class UnpinChatMessage extends BaseRequest
{
    protected function responseObject() : string
    {
        return 'bool';
    }
    
    
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
}