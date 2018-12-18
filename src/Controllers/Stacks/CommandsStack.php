<?php

namespace Blaze\Myst\Controllers\Stacks;

use Blaze\Myst\Api\Objects\Entity;
use Blaze\Myst\Api\Objects\Update;
use Blaze\Myst\Bot;
use Blaze\Myst\Controllers\BaseController;
use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Exceptions\StackException;
use Blaze\Myst\Helpers\Arr;
use Blaze\Myst\Helpers\Str;

class CommandsStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function addStackItem(BaseController $item): BaseController
    {
        if (!$item instanceof CommandController) {
            throw new StackException(get_class($item) . " must be an instance of " . CommandController::class);
        }
        
        $names = array_merge($item->getAliases(), [$item->getName()]);
        foreach ($names as $name) {
            if (array_has($this->items, $name)) {
                throw new StackException("$name has already been registered as a command.");
            }
            $this->items[$name] = $item;
        }
        return $item;
    }
    
    
    /**
     * @inheritdoc
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    public function processStack(Update $update)
    {
        $bot = $update->getBot();
        
        if (!$this->checkStackPrerequisites($bot, $update)) {
            return false;
        }
    
        foreach ($this->getStack() as $name => $command) {
            /** @var CommandController $command */
            
            $entity = $this->checkItemPrerequisites($update, $command, $name);
            
            if (!$entity) {
                continue;
            }
            
            $command->setArguments(substr($update->getMessage()->getText(), $entity->getOffset() + $entity->getLength()), $bot->getConfig('commands_param_separator'))->make($update);
        }
        
        return true;
    }
    
    
    /**
     * @inheritdoc
     * @throws \Blaze\Myst\Exceptions\ConfigurationException
     */
    protected function checkStackPrerequisites(Bot $bot, Update $update): bool
    {
        if ($bot->getConfig('process.commands') == false)  {
            return false;
        }
        
        $type = $update->detectType();
        if ($type !== 'message' && $type !== 'edited_message' && $type !== 'channel_post' && $type !== 'edited_channel_post') {
            return false;
        }
        
        if (!$update->getMessage()->has('entities')) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param Update $update
     * @param CommandController $command
     * @param string $name
     * @return bool|Entity
     */
    protected function checkItemPrerequisites(Update $update, CommandController $command, string $name)
    {
        if (!Arr::isValueTrue($command->getEngagesIn(), $update->getChat()->getType())) {
            return false;
        }
        
        $message = $update->getMessage();
        
        if ($command->isStandalone() && !Str::compareCaseInsensitive($message->getText(), str_start($name, $command->prefix()))) {
            return false;
        }
    
        $entity = $message->getEntities()->filter(function (Entity $entity) use ($message, $name, $command){
            if ($entity->getType() !== 'bot_command') {
                return false;
            }
            if (!Str::compareCaseInsensitive($entity->getText($message->getText()), str_start($name, $command->prefix()))) {
                return false;
            }
            if ($command->isCaseSensitive() && $entity->getText($message->getText()) !== str_start($name, $command->prefix())) {
                return false;
            }
            if (!$entity->inPosition($message->getText(), $command->getPosition())) {
                return false;
            }
            return true;
        })->first();
        
        return $entity;
    }
}