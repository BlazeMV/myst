<?php

namespace Blaze\Myst\Traits;

use Blaze\Myst\Controllers\CommandController;
use Blaze\Myst\Exceptions\CommandStackException;

trait StacksHandler
{
    /**
     * @var array $commands_stack
    */
    protected $commands_stack;
    
    /**
     * populates all stacks
     *
     * @param array $config
     *
     * @return $this
     * @throws CommandStackException
     */
    protected function populateStacks(array $config) {
    	$this->populateCommandsStack($config['commands']);
        
        return $this;
    }
    
    /**
     * @param array $commands
     * @return StacksHandler
     * @throws CommandStackException
     */
    protected function populateCommandsStack(array $commands)
    {
        foreach ($commands as $command_class) {
            $this->addCommandToCommandStack($command_class);
        }
        return $this;
    }
    
    /**
     * @param string $command_class
     * @throws CommandStackException
     */
    public function addCommandToCommandStack(string $command_class)
    {
        if (!class_exists($command_class)) throw new CommandStackException("class $command_class not found.");
        $command = new $command_class;
        if (!$command instanceof CommandController) throw new CommandStackException("$command_class must be an instance of " . CommandController::class);
        $names = array_merge($command->getAliases(), [$command->getName()]);
        foreach ($names as $name) {
            if (array_has($this->commands_stack, $name)) throw new CommandStackException("$name has already been registered as a command.");
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
}