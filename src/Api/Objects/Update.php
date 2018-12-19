<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Bot;

/**
 * @method CallBackQuery getCallbackQuery()
 */
class Update extends ApiObject
{
    /**
     * @var Bot $bot
    */
    protected $bot;
    
    public function setBot(Bot $bot)
    {
        $this->bot = $bot;
        return $this;
    }
    
    public function getBot()
    {
        return $this->bot;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function singleObjectRelations(): array
    {
        return [
            'message'              => Message::class,
            'edited_message'       => Message::class,
            'channel_post'         => Message::class,
            'edited_channel_post'  => Message::class,
//            'inline_query'         => InlineQuery::class,
//            'chosen_inline_result' => ChosenInlineResult::class,
            'callback_query'       => CallbackQuery::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function multipleObjectRelations(): array
    {
        return [
        
        ];
    }
    
    /**
     * Determine if the update is of given type
     *
     * @param string         $type
     *
     * @return bool
     */
    public function isType($type)
    {
        if ($this->has(strtolower($type))) {
            return true;
        }
        
        return $this->detectType() === $type;
    }
    
    /**
     * Detect type based on properties.
     *
     * @return string|null
     */
    public function detectType()
    {
        $types = [
            'message',
            'edited_message',
            'channel_post',
            'edited_channel_post',
            'inline_query',
            'chosen_inline_result',
            'callback_query',
            'shipping_query',
            'pre_checkout_query',
        ];
        
        return $this->keys()->intersect($types)->pop();
    }
    
    /**
     * Get the message contained in the Update.
     *
     * @return Message
     */
    public function getMessage()
    {
        switch ($this->detectType()) {
            case 'message':
                return $this->get('message');
            case 'edited_message':
                return $this->get('edited_message');
            case 'channel_post':
                return $this->get('channel_post');
            case 'edited_channel_post':
                return $this->get('edited_channel_post');
            case 'callback_query':
                $callbackQuery = $this->get('callback_query');
                if ($callbackQuery->has('message')) {
                    return $callbackQuery->get('message');
                }
                break;
            default:
                return null;
        }
    
        return null;
    }
    
    /**
     * Get chat object
     *
     * @return Chat
     */
    public function getChat()
    {
        return $this->getMessage() === null ? null : $this->getMessage()->getChat();
    }
    
    public function getFrom()
    {
        return $this->getMessage() === null ? null : $this->getMessage()->getFrom();
    }
}
