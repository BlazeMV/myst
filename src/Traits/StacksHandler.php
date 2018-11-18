<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Controllers\CallbackQueryController;
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
     * @var array $callback_queries_stack
     */
    protected $callback_queries_stack;
    
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
    
    /**
     * @param $name
     * @return null|CommandController
     */
    public function getCommandFromStack($name)
    {
        if (array_has($this->commands_stack, $name)) return $this->commands_stack[$name];
        return null;
    }
    
    
    /*Callback Query Stack*/
    
    /**
     * @param array $callback_queries
     * @return StacksHandler
     * @throws StackException
     */
    protected function populateCallbackQueriesStack(array $callback_queries)
    {
        foreach ($callback_queries as $cbq) {
            $this->addToCallbackQueriesStack($cbq);
        }
        return $this;
    }
    
    /**
     * @param string $cbq_class
     * @throws StackException
     */
    public function addToCallbackQueriesStack(string $cbq_class)
    {
        if (!class_exists($cbq_class)) throw new StackException("class $cbq_class not found.");
        $cbq = new $cbq_class;
        if (!$cbq instanceof CallbackQueryController) throw new StackException("$cbq_class must be an instance of " . CallbackQueryController::class);
        $names = array_merge($cbq->getAliases(), [$cbq->getName()]);
        foreach ($names as $name) {
            if (array_has($this->callback_queries_stack, $name)) throw new StackException("$name has already been registered as a callback query.");
            $this->callback_queries_stack[$name] = $cbq;
        }
    }
    
    /**
     * @return array
     */
    public function getCallbackQueriesStack(): array
    {
        return $this->callback_queries_stack;
    }
    
    /**
     * @param $name
     * @return null|CallbackQueryController
     */
    public function getCallbackQueryFromStack($name)
    {
        if (array_has($this->callback_queries_stack, $name)) return $this->callback_queries_stack[$name];
        return null;
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
        if (array_has($this->conversations_stack, $conversation->getName())) throw new StackException($conversation->getName() . " has already been registered as a conversation.");
        $this->conversations_stack[$conversation->getName()] = $conversation;
    }
    
    /**
     * @return array
     */
    public function getConversationsStack(): array
    {
        return $this->conversations_stack;
    }
    
    /**
     * @param $name
     * @return null|ConversationController
     */
    public function getConversationFromStack($name)
    {
        if (array_has($this->conversations_stack, $name)) return $this->conversations_stack[$name];
        return null;
    }
    
    
}