<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;

abstract class BaseStack implements StackInterface
{
    /**
     * @var array $items
     */
    protected $items = [];
    
    /**
     * BaseStack constructor.
     * @param array $items
     * @throws \Blaze\Myst\Exceptions\StackException
     */
    public function __construct(array $items)
    {
        $this->populateStack($items);
    }
    
    /**
     * @inheritdoc
     */
    public function populateStack(array $items): StackInterface
    {
        foreach ($items as $class) {
            $this->addStackItem(new $class);
        }
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getStack(): array
    {
        return $this->items;
    }
    
    /**
     * @inheritdoc
     */
    public function getStackItem($name): BaseController
    {
        return array_has($this->items, $name) ? $this->items[$name] : null;
    }
    
    /**
     * @param Bot $bot
     * @param Update $update
     * @return bool
     */
    abstract protected function checkStackPrerequisites(Bot $bot, Update $update): bool;
}
