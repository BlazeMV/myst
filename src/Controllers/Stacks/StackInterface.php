<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Exceptions\StackException;

interface StackInterface
{
    /**
     * @param array $items
     * @return StackInterface
     * @throws StackException
     */
    public function populateStack(array $items): StackInterface;
    
    /**
     * @param BaseController $item
     * @return BaseController
     * @throws StackException
     */
    public function addStackItem(BaseController $item): BaseController;
    
    /**
     * @return array
     */
    public function getStack(): array;
    
    /**
     * @param $name
     * @return BaseController
     */
    public function getStackItem($name): BaseController;
}