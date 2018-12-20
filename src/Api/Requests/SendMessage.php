<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\Message;
use Blaze\Myst\Api\Requests\Markup\ForceReply;
use Blaze\Myst\Api\Requests\Markup\Keyboard;

class SendMessage extends BaseRequest
{
    /**
     * @return string
     */
    protected function responseObject() : string
    {
        return Message::class;
    }
    
    /**
     * @param $chat_id
     * @return $this
     */
    public function to($chat_id)
    {
        $this->params['chat_id'] = $chat_id;
        return $this;
    }
    
    /**
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $this->params['text'] = $text;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function parseMarkdown()
    {
        $this->params['parse_mode'] = 'Markdown';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function parseHTML()
    {
        $this->params['parse_mode'] = 'HTML';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function disableWebPagePreview()
    {
        $this->params['disable_web_page_preview'] = true;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function noNotify()
    {
        $this->params['disable_notification'] = true;
        return $this;
    }
    
    /**
     * @param $message_id
     * @return $this
     */
    public function replyTo($message_id)
    {
        $this->params['reply_to_message_id'] = $message_id;
        return $this;
    }
    
    /**
     * @param $markup
     * @return $this
     */
    public function markup($markup)
    {
        if (!$markup instanceof Keyboard && !$markup instanceof ForceReply) {
            throw new \InvalidArgumentException(
                'argument 1 passed to SendMessage::markup() should be an instance of either '
                . Keyboard::class . ' or ' . ForceReply::class . '.'
            );
        }
        
        $this->params['reply_markup'] = $markup;
        return $this;
    }
}
