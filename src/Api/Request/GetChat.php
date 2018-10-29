<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\Chat;

class GetChat extends BaseRequest
{
    protected function responseObject()
    {
        return Chat::class;
    }
    
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
}