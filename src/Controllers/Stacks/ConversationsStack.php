<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\ConversationController;
use Blaze\Myst\Exceptions\StackException;

class ConversationsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof ConversationController) throw new StackException(get_class($item) . " must be an instance of " . ConversationController::class);
        if (array_has($this->items, $item->getName())) throw new StackException($item->getName() . " has already been registered as a conversation.");
        $this->items[$item->getName()] = $item;
        return $item;
    }
}