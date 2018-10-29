<?php

namespace Blaze\Myst\Api\Request;

use Blaze\Myst\Api\Objects\Raw;

class LeaveChat extends BaseRequest
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