<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\Raw;

class KickChatMember extends BaseRequest
{
    protected function responseObject()
    {
        return Raw::class;
    }
    
    
    
    public function from($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }

    public function user($user_id)
    {
        $this->params['user_id'] = $user_id;
        return $this;
    }

    public function until($unit_time)
    {
        $this->params['until_date'] = $unit_time;
        return $this;
    }
}