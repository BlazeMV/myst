<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\CallbackQueryController;
use Blaze\Myst\Exceptions\StackException;

class CallbackQueriesStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof CallbackQueryController) throw new StackException(get_class($item) . " must be an instance of " . CallbackQueryController::class);
        if (array_has($this->items, $item->getName())) throw new StackException($item->getName() . " has already been registered as a callback query.");
        $this->items[$item->getName()] = $item;
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