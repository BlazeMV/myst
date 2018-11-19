<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Exceptions\StackException;

class CommandsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof CommandController) throw new StackException(get_class($item) . " must be an instance of " . CommandController::class);
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) throw new StackException("$name has already been registered as a command.");
            $this->items[$name] = $item;
        }
        return $item;
    }
}