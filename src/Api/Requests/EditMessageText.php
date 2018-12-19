<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\Message;

class EditMessageText extends BaseRequest
{
    protected function responseObject() : string
    {
        return Message::class;
    }
    
    public function chat($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }

    public function message($message_id)
    {
        $this->params['message_id'] = $message_id;
        return $this;
    }

    public function inlineMessage($inline_message_id)
    {
        $this->params['inline_message_id'] = $inline_message_id;
        return $this;
    }

    public function text($text)
    {
        $this->params['text'] = $text;
        return $this;
    }

    public function parseMarkdown()
    {
        $this->params['parse_mode'] = 'Markdown';
        return $this;
    }

    public function parseHTML()
    {
        $this->params['parse_mode'] = 'HTML';
        return $this;
    }

    public function disableWebPagePreview()
    {
        $this->params['disable_web_page_preview'] = true;
        return $this;
    }

    public function markup($markup){
        if (!$markup instanceof Keyboard && !$markup instanceof ForceReply)
            throw new \InvalidArgumentException('$markup should be an instance of either Blaze\Myst\Api\Requests\Markup\Keyboard or Blaze\Myst\Api\Requests\Markup\ForceReply.');

        $this->params['reply_markup'] = $markup;
        return $this;
    }
}
