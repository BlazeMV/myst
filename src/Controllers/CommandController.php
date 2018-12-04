<?php

namespace Blaze\Myst\Controllers;

use Blaze\Myst\Api\Objects\Entity;

abstract class CommandController extends EntitiesController
{
    public function ratify() : bool
    {
        $names = array_merge($this->getAliases(), [$this->getName()]);
        if (!$this->canEngageIn($this->getUpdate()->getChat()->getType())) return false;
        if ($this->isStandalone() && in_array($this->get)) return false;
        
        foreach ($this->getUpdate()->getMessage()->getEntities() as $entity) {
            /**@var Entity $entity*/
            if ($entity->getType() !== 'bot_command') continue;
        
            if (strtolower($entity->getText($this->getUpdate()->getMessage()->getText())) !== str_start(strtolower($this->getName()), '/')) continue;
        
            if ($this->isCaseSensitive() && $entity->getText($this->getUpdate()->getMessage()->getText()) !== str_start($this->getName(), '/')) continue;
        
            if (!$this->entityInPosition($this->getUpdate()->getMessage()->getText(), $this->getPosition(), $entity->getOffset(), $entity->getLength())) continue;
        }
    }
}