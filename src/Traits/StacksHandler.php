<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Controllers\ConversationController;
use Blaze\Myst\Exceptions\StackException;

trait StacksHandler
{
    /**
     * @var array $commands_stack
    */
    protected $commands_stack;
    
    /**
     * @var array $conversations_stack
    */
    protected $conversations_stack;
    
    /**
     * populates all stacks
     *
     * @param array $config
     *
     * @return $this
     * @throws StackException
     */
    protected function populateStacks(array $config) {
    	$this->populateCommandsStack($config['commands']);
    	$this->populateConversationsStack($config['conversations']);
        
        return $this;
    }
    
    
    /*Commands Stack*/
    
    /**
     * @param array $commands
     * @return StacksHandler
     * @throws StackException
     */
    protected function populateCommandsStack(array $commands)
    {
        foreach ($commands as $command_class) {
            $this->addToCommandsStack($command_class);
        }
        return $this;
    }
    
    /**
     * @param string $command_class
     * @throws StackException
     */
    public function addToCommandsStack(string $command_class)
    {
        if (!class_exists($command_class)) throw new StackException("class $command_class not found.");
        $command = new $command_class;
        if (!$command instanceof CommandController) throw new StackException("$command_class must be an instance of " . CommandController::class);
        $names = array_merge($command->getAliases(), [$command->getName()]);
        foreach ($names as $name) {
            if (array_has($this->commands_stack, $name)) throw new StackException("$name has already been registered as a command.");
            $this->commands_stack[$name] = $command;
        }
    }
    
    /**
     * @return array
     */
    public function getCommandsStack(): array
    {
        return $this->commands_stack;
    }
    
    
    /*Conversations Stack*/
    
    /**
     * @param array $conversations
     * @return StacksHandler
     * @throws StackException
     */
    protected function populateConversationsStack(array $conversations)
    {
        foreach ($conversations as $conversation_class) {
            $this->addToConversationStack($conversation_class);
        }
        return $this;
    }
    
    /**
     * @param string $conversation_class
     * @throws StackException
     */
    public function addToConversationStack(string $conversation_class)
    {
        if (!class_exists($conversation_class)) throw new StackException("class $conversation_class not found.");
        $conversation = new $conversation_class;
        if (!$conversation instanceof ConversationController) throw new StackException("$conversation_class must be an instance of " . ConversationController::class);
        $this->conversations_stack[$conversation->getName()] = $conversation;
    }
    
    /**
     * @return array
     */
    public function getConversationsStack(): array
    {
        return $this->conversations_stack;
    }
    
    
}