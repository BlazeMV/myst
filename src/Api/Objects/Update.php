<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\CallbackQueryController;
use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Controllers\HashtagController;
use Blaze\Myst\Controllers\MentionController;
use Blaze\Myst\Controllers\TextController;

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
    
    protected function processTexts()
    {
        if ($this->bot->getConfig('process.texts') == false)  return true;
        
        if ($this->detectType() !== 'message' && $this->detectType() !== 'edited_message' && $this->detectType() !== 'channel_post' && $this->detectType() !== 'edited_channel_post') return true;
        
        foreach ($this->bot->getTextsStack()->getStack() as $name => $text) {
            /** @var TextController $text*/
            if (array_get($text->getEngagesIn(), $this->getChat()->getType()) == false) continue;
            if (strpos(strtolower($this->getMessage()->getText()), strtolower($name)) === false) continue;
            
            if ($text->isStandalone() && strtolower($this->getMessage()->getText()) !== strtolower($name)) continue;
            
            if ($text->isCaseSensitive() && strpos($this->getMessage()->getText(), $name) === false) continue;
            
            if (!$this->entityInPosition($this->getMessage()->getText(), $text->getPosition(), strpos($this->getMessage()->getText(), $name), strlen($name))) continue;
            
            if ($text->isStandalone()) {
                $args = [];
            } else {
                $args = $this->getArgs(substr($this->getMessage()->getText(), strpos($this->getMessage()->getText(), $name) + strlen($name)), $this->bot->getConfig('commands_param_separator')); //intentional
            }
            
            return $text->make($this->bot, $this, $args);
        }
        return true;
    }
}