<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\TextController;
use Blaze\Myst\Exceptions\StackException;

class TextsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof TextController) throw new StackException(get_class($item) . " must be an instance of " . TextController::class);
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) throw new StackException("$name has already been registered as a text.");
            $this->items[$name] = $item;
        }
        return $item;
    }
}