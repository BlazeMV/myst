<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Entity;
use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\MentionController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Helpers\Arr;
use Blaze\Myst\Helpers\Str;

class MentionsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof MentionController) {
            throw new StackException(get_class($item) . " must be an instance of " . MentionController::class);
        }
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) {
                throw new StackException("$name has already been registered as a mention.");
            }
            $this->items[$name] = $item;
        }
        return $item;
    }
    
    
    /**
     * @inheritdoc
     */
    public function processStack(Update $update)
    {
        $bot = $update->getBot();
        
        if (!$this->checkStackPrerequisites($bot, $update)) {
            return false;
        }
        
        foreach ($this->getStack() as $name => $mention) {
            /** @var MentionController $mention */
            
            $entity = $this->checkItemPrerequisites($update, $mention, $name);
            
            if (!$entity) {
                continue;
            }
    
            $mention->make($update);
        }
        
        return true;
    }
    
    
    /**
     * @inheritdoc
     */
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        if ($bot->getConfig('process.mentions') == false) {
            return false;
        }
        
        $type = $update->detectType();
        if ($type !== 'message'
            && $type !== 'edited_message'
            && $type !== 'channel_post'
            && $type !== 'edited_channel_post'
        ) {
            return false;
        }
        
        if (!$update->getMessage()->has('entities')) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param Update $update
     * @param MentionController $mention
     * @param string $name
     * @return bool|Entity
     */
    protected function checkItemPrerequisites(Update $update, MentionController $mention, string $name)
    {
        if (!Arr::isValueTrue($mention->getEngagesIn(), $update->getChat()->getType())) {
            return false;
        }
        
        $message = $update->getMessage();
        
        if ($mention->isStandalone()
            && !Str::compareCaseInsensitive($message->getText(), str_start($name, $mention->prefix()))) {
            return false;
        }
        
        $entity = $message->getEntities()->filter(function (Entity $entity) use ($message, $name, $mention) {
            if ($entity->getType() !== 'mention') {
                return false;
            }
            if (!Str::compareCaseInsensitive(
                $entity->getText($message->getText()),
                str_start($name, $mention->prefix())
            )) {
                return false;
            }
            if (!$entity->inPosition($message->getText(), $mention->getPosition())) {
                return false;
            }
            
            return true;
        })->first();
        
        return $entity;
    }
}
