<?php

namespace Blaze\Myst\Api\Requests;

use Blaze\Myst\Api\Objects\Message;
use Blaze\Myst\Api\Requests\Markup\ForceReply;
use Blaze\Myst\Api\Requests\Markup\Keyboard;

class SendAnimation extends BaseRequest
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
     * @param $animation
     * @return $this
     */
    public function animation($animation)
    {
        // Validate url
        if (filter_var(filter_var($animation, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL) !== false) {
            $animation = fopen($animation, 'r');
        }
        $this->params['animation'] = $animation;
        return $this;
    }
    
    /**
     * @param $text
     * @return $this
     */
    public function caption($text)
    {
        $this->params['caption'] = $text;
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
     * @param int $duration
     * @return $this
     */
    public function duration(int $duration)
    {
        $this->params['duration'] = $duration;
        return $this;
    }
    
    /**
     * @param int $width
     * @return $this
     */
    public function width(int $width)
    {
        $this->params['width'] = $width;
        return $this;
    }
    
    /**
     * @param int $height
     * @return $this
     */
    public function height(int $height)
    {
        $this->params['height'] = $height;
        return $this;
    }
    
    /**
     * @param $thumb
     * @return $this
     */
    public function thumb($thumb)
    {
        // Validate url
        if (filter_var(filter_var($thumb, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL) !== false) {
            $thumb = fopen($thumb, 'r');
        }
        $this->params['thumb'] = $thumb;
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
