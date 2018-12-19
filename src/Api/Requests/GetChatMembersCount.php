<?php

namespace Blaze\Myst\Api\Requests;

class GetChatMembersCount extends BaseRequest
{
    protected function responseObject() : string
    {
        return 'int';
    }
    
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
}
