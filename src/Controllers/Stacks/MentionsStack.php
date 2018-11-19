<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\MentionController;
use Blaze\Myst\Exceptions\StackException;

class MentionsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof MentionController) throw new StackException(get_class($item) . " must be an instance of " . MentionController::class);
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) throw new StackException("$name has already been registered as a mention.");
            $this->items[$name] = $item;
        }
        return $item;
    }
}