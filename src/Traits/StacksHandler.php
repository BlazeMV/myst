<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Controllers\Stacks\CallbackQueriesStack;
use Blaze\Myst\Controllers\Stacks\CommandsStack;
use Blaze\Myst\Controllers\Stacks\ConversationsStack;
use Blaze\Myst\Exceptions\StackException;

trait StacksHandler
{
    /**
     * @var CommandsStack $commands_stack
    */
    protected $commands_stack;
    
    /**
     * @var ConversationsStack $conversations_stack
    */
    protected $conversations_stack;
    
    /**
     * @var CallbackQueriesStack $callback_queries_stack
     */
    protected $callback_queries_stack;
    
    /**
     * populates all stacks
     *
     * @param array $config
     * @return $this
     * @throws StackException
     */
    protected function populateStacks(array $config) {
    	$this->commands_stack = new CommandsStack($config['commands']);
    	$this->conversations_stack = new ConversationsStack($config['conversations']);
    	$this->callback_queries_stack = new CallbackQueriesStack($config['callback_queries']);
        
        return $this;
    }
    
    /**
     * @return CommandsStack
     */
    public function getCommandsStack(): CommandsStack
    {
        return $this->commands_stack;
    }
    
    /**
     * @return ConversationsStack
     */
    public function getConversationsStack(): ConversationsStack
    {
        return $this->conversations_stack;
    }
    
    /**
     * @return CallbackQueriesStack
     */
    public function getCallbackQueriesStack(): CallbackQueriesStack
    {
        return $this->callback_queries_stack;
    }
}