<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\User;

class GetChatMember extends BaseRequest
{
    protected function responseObject() : string
    {
        return User::class;
    }
    
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }

    public function user($user_id)
    {
        $this->params['user_id'] = $user_id;
        return $this;
    }
}