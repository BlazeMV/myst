<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\User;

class GetChatAdministrators extends BaseRequest
{
    protected function responseObject()
    {
        return User::class;
    }
    
    protected function multipleResponseObjects()
    {
        return true;
    }
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
}