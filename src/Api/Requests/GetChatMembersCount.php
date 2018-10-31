<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\Raw;

class GetChatMembersCount extends BaseRequest
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
}