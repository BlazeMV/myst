<?php

namespace Blaze\Myst\Traits;

trait StacksHandler
{
    /**
     * populates all stacks
     *
     * @param array $config
     *
     * @return $this
     */
    protected function populateStacks(array $config) {
    	$this->populateCommandsStack($config['commands']);
        
        return $this;
    }
    
    protected function populateCommandsStack(array $commands)
    {
        $stack = [];
        foreach ($commands as $command_class) {
            $command = new $command_class;
            
        }
    }
}