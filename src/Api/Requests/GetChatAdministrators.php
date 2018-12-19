<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\ChatMember;

class GetChatAdministrators extends BaseRequest
{
    protected function responseObject() : string
    {
        return ChatMember::class;
    }
    
    protected function multipleResponseObjects() : bool
    {
        return true;
    }
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
}
