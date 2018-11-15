<?php

namespace Blaze\Myst\Api\Objects;

use Blaze\Myst\Api\ApiObject;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\CommandController;

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
    
    private function entityInPosition($text, $position, $offset, $length)
    {
        switch ($position) {
            case 'any':
            case '*':
                return true;
                break;
            case 'start':
            case 0:
                if ($offset == 0) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'end':
                if (strlen($text) == ($offset + $length)) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                if ((int)$position == $offset) {
                    return true;
                } else {
                    return false;
                }
        }
    }
    
    protected function getArgs($text, $separator)
    {
        if (!starts_with($separator, 'regex:')) {
            $separator = '/([^' . $separator . ']+)/';
        } else {
            $separator = str_replace('regex:', '', $separator);
        }
        
        preg_match_all($separator, $text, $matches);
        return $matches[1];
    }
    
    
    public function processUpdate()
    {
        if ($this->detectType() == ('edited_message' || 'edited_channel_post') && $this->bot->getConfig('process_edited_messages') == false) return $this;
        
        if (!$this->bot->getConfig('engages_in.' . $this->getChat()->getType())) return $this;
        
        $this->processCommand();
        
        return $this;
    }
    
    
    public function processCommand()
    {
        if ($this->bot->getConfig('process.commands') == false)  return true;
        
        if ($this->detectType() !== 'message') return true;
        
        foreach ($this->bot->getCommandsStack() as $name => $command) {
            /**@var CommandController $command*/
            foreach ($this->getMessage()->getEntities() as $entity) {
                /**@var Entity $entity*/
                if ($entity->getType() !== 'bot_command') continue;
    
                if (array_get($command->getEngagesIn(), $this->getChat()->getType()) == false) continue;
    
                if ($command->isOnlyCommand() && strtolower($this->getMessage()->getText()) !== str_start(strtolower($name), '/')) continue;
                
                if (strtolower($entity->getText($this->getMessage()->getText())) !== str_start(strtolower($name), '/')) continue;
                
                if ($command->isCaseSensitive() && $entity->getText($this->getMessage()->getText()) !== str_start($name, '/')) continue;
                
                if (!$this->entityInPosition($this->getMessage()->getText(), $command->getPosition(), $entity->getOffset(), $entity->getLength())) continue;
                
                if ($command->isOnlyCommand()) {
                    $args = [];
                } else {
                    $args = $this->getArgs(substr($this->getMessage()->getText(), $entity->getOffset() + $entity->getLength()), $this->bot->getConfig('commands_param_seperator'));
                }
                
                return $command->make($this->bot, $this, $args);
            }
        }
        return true;
    }
}