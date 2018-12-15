<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\HashtagController;
use Blaze\Myst\Exceptions\StackException;

class HashtagsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof HashtagController) throw new StackException(get_class($item) . " must be an instance of " . HashtagController::class);
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) throw new StackException("$name has already been registered as a hashtag.");
            $this->items[$name] = $item;
        }
        return $item;
    }
    
    public function processStack(Update $update)
    {
        return true;
    }
    
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        return true;
    }
    
    protected function checkItemPrerequisites(Bot $bot, Update $update, BaseController $item): bool
    {
        return true;
    }
}