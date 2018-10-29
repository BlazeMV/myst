<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Bot;

class Update extends ApiObject
{
    /**
     * @var Bot $bot
    */
    protected $bot;
    public function __construct($data, Bot $bot)
    {
        $this->bot = $bot;
        
        parent::__construct($data);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function singleObjectRelations()
    {
        return [
            'message'              => Message::class,
//            'edited_message'       => EditedMessage::class,
            'channel_post'         => Message::class,
//            'edited_channel_post'  => EditedMessage::class,
//            'inline_query'         => InlineQuery::class,
//            'chosen_inline_result' => ChosenInlineResult::class,
//            'callback_query'       => CallbackQuery::class,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function multipleObjectrelations()
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
                return $this->get('editedMessage');
            case 'channel_post':
                return $this->get('channelPost');
            case 'edited_channel_post':
                return $this->editedChannelPost;
            case 'inline_query':
                return $this->inlineQuery;
            case 'chosen_inline_result':
                return $this->chosenInlineResult;
            case 'callback_query':
                $callbackQuery = $this->callbackQuery;
                if ($callbackQuery->has('message')) {
                    return $callbackQuery->message;
                }
                break;
            case 'shipping_query':
                return $this->shippingQuery;
            case 'pre_checkout_query':
                return $this->preCheckoutQuery;
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
        if ($this->isType('message') || $this->isType('edited_message')) return $this->getMessage()->getChat();
        if ($this->isType('callback_query')) return $this->getCallbackQuery()->getMessage()->getChat();
        return null;
    }
    
    
    public function processUpdate()
    {
        return $this;
    }
}