<?php

namespace Blaze\Myst\Api\Requests;

class LeaveChat extends BaseRequest
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