<?php

namespace Blaze\Myst\Controllers;

abstract class CommandController extends EntitiesController
{
    /** @var array $arguments */
    protected $arguments;
    
    public function prefix()
    {
        return '/';
    }
    
    /**
     * @return array
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }
    
    /**
     * @param string $text
     * @param string $separator
     * @return CommandController
     */
    public function setArguments(string $text, string $separator) : CommandController
    {
        if ($this->isStandalone()) {
            $this->arguments = [];
        }
        
        if (!starts_with($separator, 'regex:')) {
            $separator = '/([^' . $separator . ']+)/';
        } else {
            $separator = str_replace('regex:', '', $separator);
        }
        
        preg_match_all($separator, $text, $matches);
        $this->arguments = $matches[1] ?? [];
        
        return $this;
    }
}
