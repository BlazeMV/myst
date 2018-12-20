<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Entity;
use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\HashtagController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Helpers\Arr;
use Blaze\Myst\Helpers\Str;

class HashtagsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof HashtagController) {
            throw new StackException(get_class($item) . " must be an instance of " . HashtagController::class);
        }
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) {
                throw new StackException("$name has already been registered as a hashtag.");
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
        
        foreach ($this->getStack() as $name => $hashtag) {
            /** @var HashtagController $hashtag */
            
            $entity = $this->checkItemPrerequisites($update, $hashtag, $name);
            
            if (!$entity) {
                continue;
            }
    
            $hashtag->make($update);
        }
        
        return true;
    }
    
    
    /**
     * @inheritdoc
     */
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        if ($bot->getConfig('process.hashtags') == false) {
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
     * @param HashtagController $hashtag
     * @param string $name
     * @return bool|Entity
     */
    protected function checkItemPrerequisites(Update $update, HashtagController $hashtag, string $name)
    {
        if (!Arr::isValueTrue($hashtag->getEngagesIn(), $update->getChat()->getType())) {
            return false;
        }
        
        $message = $update->getMessage();
        
        if ($hashtag->isStandalone()
            && !Str::compareCaseInsensitive($message->getText(), str_start($name, $hashtag->prefix()))) {
            return false;
        }
        
        $entity = $message->getEntities()->filter(function (Entity $entity) use ($message, $name, $hashtag) {
            if ($entity->getType() !== 'hashtag') {
                return false;
            }
            if (!Str::compareCaseInsensitive(
                $entity->getText($message->getText()),
                str_start($name, $hashtag->prefix())
            )) {
                return false;
            }
            if (!$entity->inPosition($message->getText(), $hashtag->getPosition())) {
                return false;
            }
            
            return true;
        })->first();
        
        return $entity;
    }
}
